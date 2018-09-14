<?php

/* preview.twig */
class __TwigTemplate_e5cba52455b4e0b36bd046796e3391c3cee1336b44a4ff7a3d2209e44afd0861 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"js-wpml-ls-preview-wrapper wpml-ls-preview-wrapper";
        if ((isset($context["class"]) ? $context["class"] : null)) {
            echo " ";
            echo twig_escape_filter($this->env, (isset($context["class"]) ? $context["class"] : null), "html", null, true);
        }
        echo "\">
    <strong class=\"wpml-ls-preview-label\">";
        // line 2
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "misc", array()), "label_preview", array()), "html", null, true);
        echo "</strong>
    <span class=\"spinner\"></span>
    <div class=\"js-wpml-ls-preview\">";
        // line 4
        echo $this->getAttribute((isset($context["preview"]) ? $context["preview"] : null), "html", array());
        echo "</div>
</div>";
    }

    public function getTemplateName()
    {
        return "preview.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  32 => 4,  27 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<div class=\"js-wpml-ls-preview-wrapper wpml-ls-preview-wrapper{% if class %} {{ class }}{% endif %}\">
    <strong class=\"wpml-ls-preview-label\">{{ strings.misc.label_preview }}</strong>
    <span class=\"spinner\"></span>
    <div class=\"js-wpml-ls-preview\">{{ preview.html|raw }}</div>
</div>", "preview.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/preview.twig");
    }
}
