<?php

/* section-footer.twig */
class __TwigTemplate_40e8493ee80d7a007e3e13d6ff6f7a5e8163c3f0b2ef5c508387bc0c67fcd6d3 extends Twig_Template
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
        echo "<p class=\"wpml-ls-form-line js-wpml-ls-option\">
    <label for=\"wpml-ls-show-in-footer\">
        <input type=\"checkbox\" id=\"wpml-ls-show-in-footer\" name=\"statics[footer][show]\" value=\"1\"
               class=\"js-wpml-ls-toggle-slot js-wpml-ls-trigger-save\" data-target=\".js-wpml-ls-footer-toggle-target\"
               ";
        // line 5
        if ($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["settings"]) ? $context["settings"] : null), "statics", array()), "footer", array()), "show", array())) {
            echo "checked=\"checked\"";
        }
        echo "/>
        ";
        // line 6
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "footer", array()), "show", array()), "html", null, true);
        echo "
    </label>

\t";
        // line 9
        $this->loadTemplate("save-notification.twig", "section-footer.twig", 9)->display($context);
        // line 10
        echo "</p>

<div class=\"js-wpml-ls-footer-toggle-target";
        // line 12
        if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["settings"]) ? $context["settings"] : null), "statics", array()), "footer", array()), "show", array()) != 1)) {
            echo " hidden";
        }
        echo "\">

\t";
        // line 14
        $context["slot_settings"] = array();
        // line 15
        echo "\t";
        $context["slot_settings"] = twig_array_merge((isset($context["slot_settings"]) ? $context["slot_settings"] : null), array("footer" => $this->getAttribute($this->getAttribute((isset($context["settings"]) ? $context["settings"] : null), "statics", array()), "footer", array())));
        // line 16
        echo "
\t";
        // line 17
        $this->loadTemplate("table-slots.twig", "section-footer.twig", 17)->display(array_merge($context, array("slot_type" => "statics", "slots_settings" =>         // line 20
(isset($context["slot_settings"]) ? $context["slot_settings"] : null), "slug" => "footer")));
        // line 24
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "section-footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  61 => 24,  59 => 20,  58 => 17,  55 => 16,  52 => 15,  50 => 14,  43 => 12,  39 => 10,  37 => 9,  31 => 6,  25 => 5,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<p class=\"wpml-ls-form-line js-wpml-ls-option\">
    <label for=\"wpml-ls-show-in-footer\">
        <input type=\"checkbox\" id=\"wpml-ls-show-in-footer\" name=\"statics[footer][show]\" value=\"1\"
               class=\"js-wpml-ls-toggle-slot js-wpml-ls-trigger-save\" data-target=\".js-wpml-ls-footer-toggle-target\"
               {% if settings.statics.footer.show %}checked=\"checked\"{% endif %}/>
        {{ strings.footer.show }}
    </label>

\t{% include 'save-notification.twig' %}
</p>

<div class=\"js-wpml-ls-footer-toggle-target{% if settings.statics.footer.show != 1 %} hidden{% endif %}\">

\t{% set slot_settings = [] %}
\t{% set slot_settings = slot_settings|merge({'footer': settings.statics.footer}) %}

\t{% include 'table-slots.twig'
\t\twith {
\t\t\t\"slot_type\": \"statics\",
\t\t\t\"slots_settings\": slot_settings,
\t        \"slug\"     : 'footer',
\t\t}
\t%}

</div>
", "section-footer.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/section-footer.twig");
    }
}
