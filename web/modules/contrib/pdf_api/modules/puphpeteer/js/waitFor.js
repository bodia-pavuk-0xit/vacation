/**
 * Wait for an event before telling Pupphpeteer that it can generate the PDF.
 */

let getPromise = null;
let promise = null;
let result = undefined;
let evalLogTries = 10;

// Based on
// https://stackoverflow.com/questions/6902334/how-to-let-javascript-wait-until-certain-event-happens
let waitFor = JSON.parse('#WAITFOR_CONFIG#');
switch (waitFor.type) {
    case 'event':
        getPromise = () => {
            return new Promise((resolve, reject) => {
                const createListener = (eventName, elementName) => {
                    let element;
                    switch (elementName) {
                        case 'window':
                            element = window;
                            break;
                        case 'document':
                            element = document;
                            break;
                        default:
                            element = document.querySelector(elementName);
                    }

                    element.addEventListener(eventName, (e) => {
                        // Return the name of the event that occurred.
                        resolve(e.type);

                        // No more polling, thanks caller.
                        return true;
                    });
                };

                waitFor.success = waitFor.success || {};
                Object.keys(waitFor.success).forEach(
                    function (elementName ) {
                        createListener(elementName, waitFor.success[elementName]);
                    }
                );

                waitFor.error = waitFor.error || {};
                Object.keys(waitFor.error).forEach(
                    function(elementName) {
                        createListener(elementName, waitFor.error[elementName]);
                    }
                );
            });
        }
        break;

    case 'document_ready':
        getPromise = () => {
            return new Promise((resolve) => {
                const listener = () => {
                    if (document.readyState == waitFor.readyState) {
                        document.removeEventListener('readystatechange', listener);
                        resolve(true);
                        return;
                    }
                }
                if (document.readyState == waitFor.readyState) {
                    resolve(true);
                    return;
                } else {
                    document.addEventListener('readystatechange', listener);
                }
            });
        }
        break;

    case 'function':
        getPromise = () => {
            return new Promise((resolve) => {
                // We will be called repeatedly from before the page content loads.
                // Don't spam logs with lots of "Can't evaluate your function"
                // messages.
                let result = false;
                try {
                    result = eval(waitFor.function);
                } catch (err) {
                    evalLogTries--;
                    if (!evalLogTries) {
                        console.log('Evaluating the function failed with result' + err);
                    }
                    setTimeout(100);
                }
                resolve(result);
            });
        }
        break;

    case 'timeout':
        getPromise = () => {
            return new Promise((resolve) => {
                setTimeout(resolve, waitFor.delay);
                resolve(true);
            });
        }
        break;

    case 'xpath':
        getPromise = () => {
            return new Promise((resolve) => {
                resolve(document.querySelector(waitFor.query));
            });
        }
        break;
}

promise = getPromise();

async function waitForSomething() {
    if (result !== undefined) {
        promise = getPromise();
    }
    result = await promise;

    if (result) {
        // Reset for another page load.
        promise = undefined;
    }

    // We will also be called again (polling) if we return false or nothing.
    return result;
}
