<?php

/* panel-colors.twig */
class __TwigTemplate_1d2b12d72e1f714af6cb4a9739e905bc2ebabbcdd58f5a1b1390b5267695d0f7 extends Twig_Template
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
        $context["wrapper_options"] = array(0 => array("label" => $this->getAttribute($this->getAttribute(        // line 2
(isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "background", array()), "name" => "background", "schemes" => array(0 => "normal"), "default" => ""), 1 => array("label" => $this->getAttribute($this->getAttribute(        // line 3
(isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "border", array()), "name" => "border", "schemes" => array(0 => "normal"), "default" => ""));
        // line 6
        echo "
";
        // line 7
        $context["options"] = array(0 => array("label" => $this->getAttribute($this->getAttribute(        // line 8
(isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "font_current", array()), "name" => "font_current", "schemes" => array(0 => "normal", 1 => "hover"), "default" => ""), 1 => array("label" => $this->getAttribute($this->getAttribute(        // line 9
(isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "background_current", array()), "name" => "background_current", "schemes" => array(0 => "normal", 1 => "hover"), "default" => ""), 2 => array("label" => $this->getAttribute($this->getAttribute(        // line 10
(isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "font_other", array()), "name" => "font_other", "schemes" => array(0 => "normal", 1 => "hover"), "default" => ""), 3 => array("label" => $this->getAttribute($this->getAttribute(        // line 11
(isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "background_other", array()), "name" => "background_other", "schemes" => array(0 => "normal", 1 => "hover"), "default" => ""));
        // line 14
        echo "
";
        // line 15
        if (((isset($context["slot_type"]) ? $context["slot_type"] : null) != "menus")) {
            // line 16
            echo "    ";
            $context["options"] = twig_array_merge((isset($context["wrapper_options"]) ? $context["wrapper_options"] : null), (isset($context["options"]) ? $context["options"] : null));
        }
        // line 18
        echo "
";
        // line 19
        $context["css_class"] = ((array_key_exists("css_class", $context)) ? (_twig_default_filter((isset($context["css_class"]) ? $context["css_class"] : null), "js-wpml-ls-colorpicker")) : ("js-wpml-ls-colorpicker"));
        // line 20
        echo "
<div class=\"js-wpml-ls-panel-colors wpml-ls-panel-colors\">
    <h4>";
        // line 22
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "panel_title", array()), "html", null, true);
        echo "</h4>

    <label for=\"wpml-ls-";
        // line 24
        echo twig_escape_filter($this->env, (isset($context["id"]) ? $context["id"] : null), "html", null, true);
        echo "-colorpicker-preset\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "label_color_preset", array()), "html", null, true);
        echo "</label>
    <select name=\"wpml-ls-";
        // line 25
        echo twig_escape_filter($this->env, (isset($context["id"]) ? $context["id"] : null), "html", null, true);
        echo "-colorpicker-preset\" class=\"js-wpml-ls-colorpicker-preset\">
        <option value=\"\">-- ";
        // line 26
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "select_option_choose", array()), "html", null, true);
        echo " --</option>
        ";
        // line 27
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["color_schemes"]) ? $context["color_schemes"] : null));
        foreach ($context['_seq'] as $context["scheme_id"] => $context["scheme"]) {
            // line 28
            echo "            <option value=\"";
            echo twig_escape_filter($this->env, $context["scheme_id"], "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["scheme"], "label", array()), "html", null, true);
            echo "</option>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['scheme_id'], $context['scheme'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 30
        echo "    </select>

    <div>
        <table>
            <tr>
                <td>
                </td>
                <th>";
        // line 37
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "label_normal_scheme", array()), "html", null, true);
        echo "</th>
                <th>";
        // line 38
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "color_picker", array()), "label_hover_scheme", array()), "html", null, true);
        echo "</th>
            </tr>
            ";
        // line 40
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["options"]) ? $context["options"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["option"]) {
            // line 41
            echo "            <tr>
                <td>";
            // line 42
            echo twig_escape_filter($this->env, $this->getAttribute($context["option"], "label", array()), "html", null, true);
            echo "</td>
                <td class=\"js-wpml-ls-colorpicker-wrapper\">
                    ";
            // line 44
            if (twig_in_filter("normal", $this->getAttribute($context["option"], "schemes", array()))) {
                // line 45
                echo "                        ";
                if ((isset($context["name_base"]) ? $context["name_base"] : null)) {
                    // line 46
                    echo "                            ";
                    $context["input_name"] = ((((isset($context["name_base"]) ? $context["name_base"] : null) . "[") . $this->getAttribute($context["option"], "name", array())) . "_normal]");
                    // line 47
                    echo "                        ";
                } else {
                    // line 48
                    echo "                            ";
                    $context["input_name"] = ($this->getAttribute($context["option"], "name", array()) . "_normal");
                    // line 49
                    echo "                        ";
                }
                // line 50
                echo "                        <input class=\"";
                echo twig_escape_filter($this->env, (isset($context["css_class"]) ? $context["css_class"] : null), "html", null, true);
                echo " js-wpml-ls-color-";
                echo twig_escape_filter($this->env, $this->getAttribute($context["option"], "name", array()), "html", null, true);
                echo "_normal\" type=\"text\" size=\"7\"
                               id=\"wpml-ls-";
                // line 51
                echo twig_escape_filter($this->env, (isset($context["id"]) ? $context["id"] : null), "html", null, true);
                echo "-";
                echo twig_escape_filter($this->env, $this->getAttribute($context["option"], "name", array()), "html", null, true);
                echo "-normal\" name=\"";
                echo twig_escape_filter($this->env, (isset($context["input_name"]) ? $context["input_name"] : null), "html", null, true);
                echo "\"
                               value=\"";
                // line 52
                echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["slot_settings"]) ? $context["slot_settings"] : null), ($this->getAttribute($context["option"], "name", array()) . "_normal"), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["slot_settings"]) ? $context["slot_settings"] : null), ($this->getAttribute($context["option"], "name", array()) . "_normal"), array(), "array"), $this->getAttribute($context["option"], "default", array()))) : ($this->getAttribute($context["option"], "default", array()))), "html", null, true);
                echo "\" data-default-color=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["option"], "default", array()), "html", null, true);
                echo "\" style=\"display: none;\">
                    ";
            }
            // line 54
            echo "                </td>
                <td class=\"js-wpml-ls-colorpicker-wrapper\">
                    ";
            // line 56
            if (twig_in_filter("hover", $this->getAttribute($context["option"], "schemes", array()))) {
                // line 57
                echo "                        ";
                if ((isset($context["name_base"]) ? $context["name_base"] : null)) {
                    // line 58
                    echo "                            ";
                    $context["input_name"] = ((((isset($context["name_base"]) ? $context["name_base"] : null) . "[") . $this->getAttribute($context["option"], "name", array())) . "_hover]");
                    // line 59
                    echo "                        ";
                } else {
                    // line 60
                    echo "                            ";
                    $context["input_name"] = ($this->getAttribute($context["option"], "name", array()) . "_hover");
                    // line 61
                    echo "                        ";
                }
                // line 62
                echo "                        <input class=\"";
                echo twig_escape_filter($this->env, (isset($context["css_class"]) ? $context["css_class"] : null), "html", null, true);
                echo " js-wpml-ls-color-";
                echo twig_escape_filter($this->env, $this->getAttribute($context["option"], "name", array()), "html", null, true);
                echo "_hover\" type=\"text\" size=\"7\"
                               id=\"wpml-ls-";
                // line 63
                echo twig_escape_filter($this->env, (isset($context["id"]) ? $context["id"] : null), "html", null, true);
                echo "-";
                echo twig_escape_filter($this->env, $this->getAttribute($context["option"], "name", array()), "html", null, true);
                echo "-hover\" name=\"";
                echo twig_escape_filter($this->env, (isset($context["input_name"]) ? $context["input_name"] : null), "html", null, true);
                echo "\"
                               value=\"";
                // line 64
                echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["slot_settings"]) ? $context["slot_settings"] : null), ($this->getAttribute($context["option"], "name", array()) . "_hover"), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["slot_settings"]) ? $context["slot_settings"] : null), ($this->getAttribute($context["option"], "name", array()) . "_hover"), array(), "array"), $this->getAttribute($context["option"], "default", array()))) : ($this->getAttribute($context["option"], "default", array()))), "html", null, true);
                echo "\" data-default-color=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["option"], "default", array()), "html", null, true);
                echo "\" style=\"display: none;\">
                    ";
            }
            // line 66
            echo "                </td>
            </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['option'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 69
        echo "        </table>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "panel-colors.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  204 => 69,  196 => 66,  189 => 64,  181 => 63,  174 => 62,  171 => 61,  168 => 60,  165 => 59,  162 => 58,  159 => 57,  157 => 56,  153 => 54,  146 => 52,  138 => 51,  131 => 50,  128 => 49,  125 => 48,  122 => 47,  119 => 46,  116 => 45,  114 => 44,  109 => 42,  106 => 41,  102 => 40,  97 => 38,  93 => 37,  84 => 30,  73 => 28,  69 => 27,  65 => 26,  61 => 25,  55 => 24,  50 => 22,  46 => 20,  44 => 19,  41 => 18,  37 => 16,  35 => 15,  32 => 14,  30 => 11,  29 => 10,  28 => 9,  27 => 8,  26 => 7,  23 => 6,  21 => 3,  20 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% set wrapper_options =  [
        {'label': strings.color_picker.background,         'name': 'background',         'schemes': ['normal'],          'default': '' },
        {'label': strings.color_picker.border,             'name': 'border',             'schemes': ['normal'],          'default': '' },
    ]
%}

{% set options = [
        {'label': strings.color_picker.font_current,       'name': 'font_current',       'schemes': ['normal', 'hover'], 'default': '' },
        {'label': strings.color_picker.background_current, 'name': 'background_current', 'schemes': ['normal', 'hover'], 'default': '' },
        {'label': strings.color_picker.font_other,         'name': 'font_other',         'schemes': ['normal', 'hover'], 'default': '' },
        {'label': strings.color_picker.background_other,   'name': 'background_other',   'schemes': ['normal', 'hover'], 'default': '' },
    ]
%}

{% if slot_type != 'menus' %}
    {% set options = wrapper_options|merge(options) %}
{% endif %}

{% set css_class = css_class|default( 'js-wpml-ls-colorpicker' ) %}

<div class=\"js-wpml-ls-panel-colors wpml-ls-panel-colors\">
    <h4>{{ strings.color_picker.panel_title }}</h4>

    <label for=\"wpml-ls-{{ id }}-colorpicker-preset\">{{ strings.color_picker.label_color_preset }}</label>
    <select name=\"wpml-ls-{{ id }}-colorpicker-preset\" class=\"js-wpml-ls-colorpicker-preset\">
        <option value=\"\">-- {{ strings.color_picker.select_option_choose }} --</option>
        {% for scheme_id, scheme in color_schemes %}
            <option value=\"{{ scheme_id }}\">{{ scheme.label }}</option>
        {% endfor %}
    </select>

    <div>
        <table>
            <tr>
                <td>
                </td>
                <th>{{ strings.color_picker.label_normal_scheme }}</th>
                <th>{{ strings.color_picker.label_hover_scheme }}</th>
            </tr>
            {% for option in options %}
            <tr>
                <td>{{ option.label }}</td>
                <td class=\"js-wpml-ls-colorpicker-wrapper\">
                    {% if 'normal' in option.schemes %}
                        {% if name_base %}
                            {% set input_name = name_base ~ '[' ~ option.name ~ '_normal]' %}
                        {% else %}
                            {% set input_name =  option.name ~ '_normal' %}
                        {% endif %}
                        <input class=\"{{ css_class }} js-wpml-ls-color-{{ option.name }}_normal\" type=\"text\" size=\"7\"
                               id=\"wpml-ls-{{ id }}-{{ option.name }}-normal\" name=\"{{ input_name }}\"
                               value=\"{{ slot_settings[ option.name ~ \"_normal\" ]|default( option.default ) }}\" data-default-color=\"{{ option.default }}\" style=\"display: none;\">
                    {% endif %}
                </td>
                <td class=\"js-wpml-ls-colorpicker-wrapper\">
                    {% if 'hover' in option.schemes %}
                        {% if name_base %}
                            {% set input_name = name_base ~ '[' ~ option.name ~ '_hover]' %}
                        {% else %}
                            {% set input_name =  option.name ~ '_hover' %}
                        {% endif %}
                        <input class=\"{{ css_class }} js-wpml-ls-color-{{ option.name }}_hover\" type=\"text\" size=\"7\"
                               id=\"wpml-ls-{{ id }}-{{ option.name }}-hover\" name=\"{{ input_name }}\"
                               value=\"{{ slot_settings[ option.name ~ \"_hover\" ]|default( option.default ) }}\" data-default-color=\"{{ option.default }}\" style=\"display: none;\">
                    {% endif %}
                </td>
            </tr>
            {% endfor %}
        </table>
    </div>
</div>", "panel-colors.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/panel-colors.twig");
    }
}
