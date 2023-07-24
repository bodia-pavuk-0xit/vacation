<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/contrib/printable/templates/printable.html.twig */
class __TwigTemplate_11bd0282f25a146f3500d9c9487e6ec8 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 15
        echo "<!DOCTYPE html>
<html";
        // line 16
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["html_attributes"] ?? null), 16, $this->source), "html", null, true);
        echo ">
  <head>
    <title>";
        // line 18
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 18, $this->source), "html", null, true);
        echo "</title>
    <style>
    .node_view ul li{
    display:none;
    }
    </style>
    <link type=\"text/css\" rel=\"stylesheet\" href=\"";
        // line 24
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["base_url"] ?? null), 24, $this->source), "html", null, true);
        echo "/css/drupal-printable.css\" />
      ";
        // line 25
        if (($context["include_css"] ?? null)) {
            // line 26
            echo "        <link type=\"text/css\" rel=\"stylesheet\" href=\"/";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["include_css"] ?? null), 26, $this->source), "html", null, true);
            echo "\" />
      ";
        }
        // line 28
        echo "      ";
        if (($context["close_script"] ?? null)) {
            // line 29
            echo "        <script type=\"text/javascript\" src=\"";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["close_script"] ?? null), 29, $this->source), "html", null, true);
            echo "\"></script>
      ";
        } else {
            // line 31
            echo "        <script type=\"text/javascript\" src=\"";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["send_script"] ?? null), 31, $this->source), "html", null, true);
            echo "\"></script>
      ";
        }
        // line 33
        echo "  </head>
  <body>
  ";
        // line 35
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["header"] ?? null), 35, $this->source), "html", null, true);
        echo "
  ";
        // line 36
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["content"] ?? null), 36, $this->source), "html", null, true);
        echo "
  ";
        // line 37
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["footer"] ?? null), 37, $this->source), "html", null, true);
        echo "
  </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "modules/contrib/printable/templates/printable.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  95 => 37,  91 => 36,  87 => 35,  83 => 33,  77 => 31,  71 => 29,  68 => 28,  62 => 26,  60 => 25,  56 => 24,  47 => 18,  42 => 16,  39 => 15,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/contrib/printable/templates/printable.html.twig", "/var/www/web/modules/contrib/printable/templates/printable.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 25);
        static $filters = array("escape" => 16);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
