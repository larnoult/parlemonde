<?php

/* slot-subform-sidebars.twig */
class __TwigTemplate_ce83c697124d258eb7a0fa81bd15ce636d5e561c196e460ef6ea24b57494cb4a extends Twig_Template
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
        if ( !array_key_exists("slot_settings", $context)) {
            // line 2
            echo "    ";
            $context["slot_settings"] = (isset($context["default_sidebars_slot"]) ? $context["default_sidebars_slot"] : null);
        }
        // line 4
        echo "
";
        // line 5
        $this->loadTemplate("preview.twig", "slot-subform-sidebars.twig", 5)->display(array_merge($context, array("preview" => (isset($context["preview"]) ? $context["preview"] : null))));
        // line 6
        echo "
<div class=\"wpml-ls-subform-options\">

    ";
        // line 9
        $this->loadTemplate("dropdown-sidebars.twig", "slot-subform-sidebars.twig", 9)->display(array_merge($context, array("slug" =>         // line 11
(isset($context["slug"]) ? $context["slug"] : null), "settings" =>         // line 12
(isset($context["settings"]) ? $context["settings"] : null), "sidebars" =>         // line 13
(isset($context["slots"]) ? $context["slots"] : null), "strings" =>         // line 14
(isset($context["strings"]) ? $context["strings"] : null))));
        // line 17
        echo "
    ";
        // line 18
        $this->loadTemplate("dropdown-templates.twig", "slot-subform-sidebars.twig", 18)->display(array_merge($context, array("id" => ("in-sidebars-" .         // line 20
(isset($context["slug"]) ? $context["slug"] : null)), "name" => (("sidebars[" .         // line 21
(isset($context["slug"]) ? $context["slug"] : null)) . "][template]"), "value" => $this->getAttribute(        // line 22
(isset($context["slot_settings"]) ? $context["slot_settings"] : null), "template", array()), "slot_type" => "sidebars")));
        // line 26
        echo "
    ";
        // line 27
        $this->loadTemplate("checkboxes-includes.twig", "slot-subform-sidebars.twig", 27)->display(array_merge($context, array("name_base" => (("sidebars[" .         // line 29
(isset($context["slug"]) ? $context["slug"] : null)) . "]"), "slot_settings" =>         // line 30
(isset($context["slot_settings"]) ? $context["slot_settings"] : null), "strings" =>         // line 31
(isset($context["strings"]) ? $context["strings"] : null), "template_slug" => $this->getAttribute(        // line 32
(isset($context["slot_settings"]) ? $context["slot_settings"] : null), "template", array()))));
        // line 35
        echo "
    <h4><label for=\"widget-title-in-";
        // line 36
        echo twig_escape_filter($this->env, (isset($context["slug"]) ? $context["slug"] : null), "html", null, true);
        echo "\">
            ";
        // line 37
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "sidebars", array()), "label_widget_title", array()), "html", null, true);
        echo "  ";
        $this->loadTemplate("tooltip.twig", "slot-subform-sidebars.twig", 37)->display(array_merge($context, array("content" => $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "tooltips", array()), "widget_title", array()))));
        echo "</label></h4>

    <input type=\"text\" id=\"widget-title-in-";
        // line 39
        echo twig_escape_filter($this->env, (isset($context["slug"]) ? $context["slug"] : null), "html", null, true);
        echo "\"
           name=\"sidebars[";
        // line 40
        echo twig_escape_filter($this->env, (isset($context["slug"]) ? $context["slug"] : null), "html", null, true);
        echo "][widget_title]\" value=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["slot_settings"]) ? $context["slot_settings"] : null), "widget_title", array()), "html", null, true);
        echo "\" size=\"40\">


    ";
        // line 43
        $this->loadTemplate("panel-colors.twig", "slot-subform-sidebars.twig", 43)->display(array_merge($context, array("strings" =>         // line 45
(isset($context["strings"]) ? $context["strings"] : null), "id" => ("in-sidebars-" .         // line 46
(isset($context["slug"]) ? $context["slug"] : null)), "name_base" => (("sidebars[" .         // line 47
(isset($context["slug"]) ? $context["slug"] : null)) . "]"), "slot_settings" =>         // line 48
(isset($context["slot_settings"]) ? $context["slot_settings"] : null), "color_schemes" =>         // line 49
(isset($context["color_schemes"]) ? $context["color_schemes"] : null))));
        // line 52
        echo "
</div>";
    }

    public function getTemplateName()
    {
        return "slot-subform-sidebars.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  91 => 52,  89 => 49,  88 => 48,  87 => 47,  86 => 46,  85 => 45,  84 => 43,  76 => 40,  72 => 39,  65 => 37,  61 => 36,  58 => 35,  56 => 32,  55 => 31,  54 => 30,  53 => 29,  52 => 27,  49 => 26,  47 => 22,  46 => 21,  45 => 20,  44 => 18,  41 => 17,  39 => 14,  38 => 13,  37 => 12,  36 => 11,  35 => 9,  30 => 6,  28 => 5,  25 => 4,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% if not slot_settings is defined %}
    {% set slot_settings = default_sidebars_slot %}
{% endif %}

{% include 'preview.twig' with {\"preview\": preview } %}

<div class=\"wpml-ls-subform-options\">

    {% include 'dropdown-sidebars.twig'
        with {
            \"slug\":     slug,
            \"settings\": settings,
            \"sidebars\": slots,
            \"strings\": strings,
        }
    %}

    {% include 'dropdown-templates.twig'
        with {
            \"id\": \"in-sidebars-\" ~ slug,
            \"name\": \"sidebars[\" ~ slug ~ \"][template]\",
            \"value\": slot_settings.template,
            \"slot_type\": \"sidebars\",
        }
    %}

    {% include 'checkboxes-includes.twig'
        with {
            \"name_base\": \"sidebars[\" ~ slug ~ \"]\",
            \"slot_settings\": slot_settings,
            \"strings\": strings,
            \"template_slug\": slot_settings.template,
        }
    %}

    <h4><label for=\"widget-title-in-{{ slug }}\">
            {{ strings.sidebars.label_widget_title }}  {% include 'tooltip.twig' with { \"content\": strings.tooltips.widget_title } %}</label></h4>

    <input type=\"text\" id=\"widget-title-in-{{ slug }}\"
           name=\"sidebars[{{ slug }}][widget_title]\" value=\"{{ slot_settings.widget_title }}\" size=\"40\">


    {% include 'panel-colors.twig'
        with {
            \"strings\": strings,
            \"id\": \"in-sidebars-\" ~ slug,
            \"name_base\": \"sidebars[\" ~ slug ~ \"]\",
            \"slot_settings\": slot_settings,
            \"color_schemes\": color_schemes,
        }
    %}

</div>", "slot-subform-sidebars.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/slot-subform-sidebars.twig");
    }
}
