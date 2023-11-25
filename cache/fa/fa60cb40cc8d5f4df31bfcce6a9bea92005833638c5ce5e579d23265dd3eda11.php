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

/* hello.html */
class __TwigTemplate_eca6cbc1139fc5f6a18cacd8ac51a0e596508b83f75c798a433603071ad10ab8 extends Template
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
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "Hello, ";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "! You are ";
        echo twig_escape_filter($this->env, ($context["age"] ?? null), "html", null, true);
        echo " years old!";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "hello.html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "hello.html", "/u/relis/public_html/views/hello.html");
    }
}
