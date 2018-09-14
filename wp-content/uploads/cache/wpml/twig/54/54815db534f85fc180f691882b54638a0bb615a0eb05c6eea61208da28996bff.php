<?php

/* section-shortcode-action.twig */
class __TwigTemplate_706fbbb5bc9688b436e9cfc012a1a154ae34ae0cc5286aa56ed10b8b68965b6a extends Twig_Template
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
        echo "<p>";
        echo $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "shortcode_actions", array()), "section_description", array());
        echo "</p>

<div class=\"hidden\">
    ";
        // line 4
        $context["slot_settings"] = array();
        // line 5
        echo "    ";
        $context["slot_settings"] = twig_array_merge((isset($context["slot_settings"]) ? $context["slot_settings"] : null), array("shortcode_actions" => $this->getAttribute($this->getAttribute((isset($context["settings"]) ? $context["settings"] : null), "statics", array()), "footer", array())));
        // line 6
        echo "
    ";
        // line 7
        $this->loadTemplate("table-slots.twig", "section-shortcode-action.twig", 7)->display(array_merge($context, array("slot_type" => "statics", "slots_settings" =>         // line 10
(isset($context["slot_settings"]) ? $context["slot_settings"] : null), "slug" => "shortcode_actions")));
        // line 14
        echo "
</div>

<div class=\"alignleft\">
    <button class=\"js-wpml-ls-open-dialog button-secondary\"
            data-target=\"#wpml-ls-slot-list-statics-shortcode_actions\"
            name=\"wpml-ls-customize\">";
        // line 20
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "shortcode_actions", array()), "customize_button_label", array()), "html", null, true);
        echo "</button>
</div>";
    }

    public function getTemplateName()
    {
        return "section-shortcode-action.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 20,  37 => 14,  35 => 10,  34 => 7,  31 => 6,  28 => 5,  26 => 4,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<p>{{ strings.shortcode_actions.section_description|raw }}</p>

<div class=\"hidden\">
    {% set slot_settings = [] %}
    {% set slot_settings = slot_settings|merge({'shortcode_actions': settings.statics.footer}) %}

    {% include 'table-slots.twig'
        with {
            \"slot_type\": \"statics\",
            \"slots_settings\": slot_settings,
            \"slug\"     : 'shortcode_actions',
        }
    %}

</div>

<div class=\"alignleft\">
    <button class=\"js-wpml-ls-open-dialog button-secondary\"
            data-target=\"#wpml-ls-slot-list-statics-shortcode_actions\"
            name=\"wpml-ls-customize\">{{ strings.shortcode_actions.customize_button_label }}</button>
</div>", "section-shortcode-action.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/section-shortcode-action.twig");
    }
}
