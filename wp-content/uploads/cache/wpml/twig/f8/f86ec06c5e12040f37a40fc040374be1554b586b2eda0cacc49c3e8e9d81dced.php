<?php

/* table-slot-row.twig */
class __TwigTemplate_8c58873604bf16ce89f2ee4c578226871fd4cfe547a8a88baf4d08b1e54947b3 extends Twig_Template
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
        if (((isset($context["slot_type"]) ? $context["slot_type"] : null) == "statics")) {
            // line 2
            echo "\t";
            $context["is_static"] = true;
            // line 3
            echo "\t";
            $context["dialog_title"] = $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), (isset($context["slug"]) ? $context["slug"] : null), array(), "array"), "dialog_title", array());
            // line 4
            echo "\t";
            $context["include_row"] = (((("slot-subform-" . (isset($context["slot_type"]) ? $context["slot_type"] : null)) . "-") . (isset($context["slug"]) ? $context["slug"] : null)) . ".twig");
        } else {
            // line 6
            echo "\t";
            $context["dialog_title"] = $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), (isset($context["slot_type"]) ? $context["slot_type"] : null), array(), "array"), "dialog_title", array());
            // line 7
            echo "\t";
            $context["include_row"] = (("slot-subform-" . (isset($context["slot_type"]) ? $context["slot_type"] : null)) . ".twig");
        }
        // line 9
        echo "
";
        // line 10
        $context["slot_row_id"] = ((("wpml-ls-" . (isset($context["slot_type"]) ? $context["slot_type"] : null)) . "-row-") . (isset($context["slug"]) ? $context["slug"] : null));
        // line 11
        echo "<tr id=\"";
        echo twig_escape_filter($this->env, (isset($context["slot_row_id"]) ? $context["slot_row_id"] : null), "html", null, true);
        echo "\" class=\"js-wpml-ls-row\" data-item-slug=\"";
        echo twig_escape_filter($this->env, (isset($context["slug"]) ? $context["slug"] : null), "html", null, true);
        echo "\" data-item-type=\"";
        echo twig_escape_filter($this->env, (isset($context["slot_type"]) ? $context["slot_type"] : null), "html", null, true);
        echo "\">
    <td class=\"wpml-ls-cell-preview\">
        <div class=\"js-wpml-ls-subform wpml-ls-subform\" data-origin-id=\"";
        // line 13
        echo twig_escape_filter($this->env, (isset($context["slot_row_id"]) ? $context["slot_row_id"] : null), "html", null, true);
        echo "\" data-title=\"";
        echo twig_escape_filter($this->env, (isset($context["dialog_title"]) ? $context["dialog_title"] : null), "html", null, true);
        echo "\" data-item-slug=\"";
        echo twig_escape_filter($this->env, (isset($context["slug"]) ? $context["slug"] : null), "html", null, true);
        echo "\" data-item-type=\"";
        echo twig_escape_filter($this->env, (isset($context["slot_type"]) ? $context["slot_type"] : null), "html", null, true);
        echo "\">
            ";
        // line 14
        if ((isset($context["slot_settings"]) ? $context["slot_settings"] : null)) {
            // line 15
            echo "                ";
            $this->loadTemplate((isset($context["include_row"]) ? $context["include_row"] : null), "table-slot-row.twig", 15)->display(array_merge($context, array("slug" =>             // line 17
(isset($context["slug"]) ? $context["slug"] : null), "slot_settings" =>             // line 18
(isset($context["slot_settings"]) ? $context["slot_settings"] : null), "settings" =>             // line 19
(isset($context["settings"]) ? $context["settings"] : null), "slots" =>             // line 20
(isset($context["slots"]) ? $context["slots"] : null), "strings" =>             // line 21
(isset($context["strings"]) ? $context["strings"] : null), "preview" => $this->getAttribute($this->getAttribute(            // line 22
(isset($context["previews"]) ? $context["previews"] : null), (isset($context["slot_type"]) ? $context["slot_type"] : null), array(), "array"), (isset($context["slug"]) ? $context["slug"] : null), array(), "array"), "color_schemes" =>             // line 23
(isset($context["color_schemes"]) ? $context["color_schemes"] : null))));
            // line 26
            echo "            ";
        }
        // line 27
        echo "        </div>
    </td>

\t";
        // line 30
        if ( !(isset($context["is_static"]) ? $context["is_static"] : null)) {
            // line 31
            echo "    <td>
        <span class=\"js-wpml-ls-row-title\">";
            // line 32
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["slots"]) ? $context["slots"] : null), (isset($context["slug"]) ? $context["slug"] : null), array(), "array"), "name", array()), "html", null, true);
            echo "</span>
    </td>
\t";
        }
        // line 35
        echo "
\t<td class=\"wpml-ls-cell-action\">
        <a href=\"#\" title=\"";
        // line 37
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "misc", array()), "title_action_edit", array()), "html", null, true);
        echo "\" class=\"js-wpml-ls-row-edit wpml-ls-row-edit\"><i class=\"otgs-ico-edit\"></i></a>
    </td>

\t";
        // line 40
        if ( !(isset($context["is_static"]) ? $context["is_static"] : null)) {
            // line 41
            echo "    <td class=\"wpml-ls-cell-action\">
        <a href=\"#\" title=\"";
            // line 42
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "misc", array()), "title_action_delete", array()), "html", null, true);
            echo "\" class=\"js-wpml-ls-row-remove wpml-ls-row-remove\"><i class=\"otgs-ico-delete\"></i></a>
    </td>
\t";
        }
        // line 45
        echo "</tr>";
    }

    public function getTemplateName()
    {
        return "table-slot-row.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  115 => 45,  109 => 42,  106 => 41,  104 => 40,  98 => 37,  94 => 35,  88 => 32,  85 => 31,  83 => 30,  78 => 27,  75 => 26,  73 => 23,  72 => 22,  71 => 21,  70 => 20,  69 => 19,  68 => 18,  67 => 17,  65 => 15,  63 => 14,  53 => 13,  43 => 11,  41 => 10,  38 => 9,  34 => 7,  31 => 6,  27 => 4,  24 => 3,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% if slot_type == 'statics' %}
\t{% set is_static = true %}
\t{% set dialog_title = strings[ slug ].dialog_title %}
\t{% set include_row  = 'slot-subform-' ~ slot_type ~ '-' ~ slug ~ '.twig' %}
{% else %}
\t{% set dialog_title = strings[ slot_type ].dialog_title %}
\t{% set include_row  = 'slot-subform-' ~ slot_type ~ '.twig' %}
{% endif %}

{% set slot_row_id = 'wpml-ls-' ~ slot_type ~ '-row-' ~ slug %}
<tr id=\"{{ slot_row_id }}\" class=\"js-wpml-ls-row\" data-item-slug=\"{{ slug }}\" data-item-type=\"{{ slot_type }}\">
    <td class=\"wpml-ls-cell-preview\">
        <div class=\"js-wpml-ls-subform wpml-ls-subform\" data-origin-id=\"{{ slot_row_id }}\" data-title=\"{{ dialog_title }}\" data-item-slug=\"{{ slug }}\" data-item-type=\"{{ slot_type }}\">
            {% if slot_settings %}
                {% include include_row
                    with {
                        \"slug\":     slug,
                        \"slot_settings\": slot_settings,
                        \"settings\": settings,
                        \"slots\":    slots,
                        \"strings\": strings,
                        \"preview\": previews[ slot_type ][ slug ],
                        \"color_schemes\": color_schemes,
                    }
                %}
            {% endif %}
        </div>
    </td>

\t{% if not is_static %}
    <td>
        <span class=\"js-wpml-ls-row-title\">{{ slots[ slug ].name }}</span>
    </td>
\t{% endif %}

\t<td class=\"wpml-ls-cell-action\">
        <a href=\"#\" title=\"{{ strings.misc.title_action_edit }}\" class=\"js-wpml-ls-row-edit wpml-ls-row-edit\"><i class=\"otgs-ico-edit\"></i></a>
    </td>

\t{% if not is_static %}
    <td class=\"wpml-ls-cell-action\">
        <a href=\"#\" title=\"{{ strings.misc.title_action_delete }}\" class=\"js-wpml-ls-row-remove wpml-ls-row-remove\"><i class=\"otgs-ico-delete\"></i></a>
    </td>
\t{% endif %}
</tr>", "table-slot-row.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/table-slot-row.twig");
    }
}
