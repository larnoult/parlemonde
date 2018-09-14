<?php

/* button-add-new-ls.twig */
class __TwigTemplate_4b988996f4ec8828cdc52ab545c33b27f4384b81a3b001db6180e1701fb24cb6 extends Twig_Template
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
        echo "<p class=\"alignright\">

\t";
        // line 3
        $context["add_tooltip"] = (isset($context["tooltip_all_assigned"]) ? $context["tooltip_all_assigned"] : null);
        // line 4
        echo "
\t";
        // line 5
        if (((isset($context["existing_items"]) ? $context["existing_items"] : null) == 0)) {
            // line 6
            echo "\t\t";
            $context["add_tooltip"] = (isset($context["tooltip_no_item"]) ? $context["tooltip_no_item"] : null);
            // line 7
            echo "\t";
        }
        // line 8
        echo "
\t";
        // line 9
        if (((isset($context["settings_items"]) ? $context["settings_items"] : null) >= (isset($context["existing_items"]) ? $context["existing_items"] : null))) {
            // line 10
            echo "\t\t";
            $context["disabled"] = true;
            // line 11
            echo "\t";
        }
        // line 12
        echo "
\t<span class=\"js-wpml-ls-tooltip-wrapper";
        // line 13
        if ( !(isset($context["disabled"]) ? $context["disabled"] : null)) {
            echo " hidden";
        }
        echo "\">
        ";
        // line 14
        $this->loadTemplate("tooltip.twig", "button-add-new-ls.twig", 14)->display(array_merge($context, array("content" => (isset($context["add_tooltip"]) ? $context["add_tooltip"] : null))));
        // line 15
        echo "    </span>

\t<button class=\"js-wpml-ls-open-dialog button-secondary\"";
        // line 17
        if ((isset($context["disabled"]) ? $context["disabled"] : null)) {
            echo " disabled=\"disabled\"";
        }
        // line 18
        echo "\t\t\tdata-target=\"";
        echo twig_escape_filter($this->env, (isset($context["button_target"]) ? $context["button_target"] : null), "html", null, true);
        echo "\">+ ";
        echo twig_escape_filter($this->env, (isset($context["button_label"]) ? $context["button_label"] : null), "html", null, true);
        echo "</button>
</p>";
    }

    public function getTemplateName()
    {
        return "button-add-new-ls.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 18,  62 => 17,  58 => 15,  56 => 14,  50 => 13,  47 => 12,  44 => 11,  41 => 10,  39 => 9,  36 => 8,  33 => 7,  30 => 6,  28 => 5,  25 => 4,  23 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<p class=\"alignright\">

\t{% set add_tooltip = tooltip_all_assigned %}

\t{% if  existing_items == 0 %}
\t\t{% set add_tooltip = tooltip_no_item %}
\t{% endif %}

\t{% if settings_items >=  existing_items %}
\t\t{% set disabled = true %}
\t{% endif %}

\t<span class=\"js-wpml-ls-tooltip-wrapper{% if not disabled %} hidden{% endif %}\">
        {% include 'tooltip.twig' with { \"content\": add_tooltip } %}
    </span>

\t<button class=\"js-wpml-ls-open-dialog button-secondary\"{% if disabled %} disabled=\"disabled\"{% endif %}
\t\t\tdata-target=\"{{ button_target }}\">+ {{ button_label }}</button>
</p>", "button-add-new-ls.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/button-add-new-ls.twig");
    }
}
