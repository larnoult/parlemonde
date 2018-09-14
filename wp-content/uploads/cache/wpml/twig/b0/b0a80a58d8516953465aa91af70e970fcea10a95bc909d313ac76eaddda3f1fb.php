<?php

/* dropdown-menus.twig */
class __TwigTemplate_11fc1141883b454d9d9886e4d273df128d06e1de83134ea8affa040b48165094 extends Twig_Template
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
        echo "<h4><label for=\"wpml-ls-available-menus\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "menus", array()), "select_label", array()), "html", null, true);
        echo ":</label>  ";
        $this->loadTemplate("tooltip.twig", "dropdown-menus.twig", 1)->display(array_merge($context, array("content" => $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "tooltips", array()), "available_menus", array()))));
        echo "</h4>
<select name=\"wpml_ls_available_menus\" class=\"js-wpml-ls-available-slots js-wpml-ls-available-menus\">
    <option disabled=\"disabled\">-- ";
        // line 3
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "menus", array()), "select_option_choose", array()), "html", null, true);
        echo " --</option>
    ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["menus"]) ? $context["menus"] : null));
        foreach ($context['_seq'] as $context["menu_key"] => $context["menu"]) {
            // line 5
            echo "        ";
            if (($context["menu_key"] == (isset($context["slug"]) ? $context["slug"] : null))) {
                // line 6
                echo "            ";
                $context["attr"] = " selected=\"selected\"";
                // line 7
                echo "        ";
            } elseif (twig_in_filter($this->getAttribute($context["menu"], "term_id", array()), twig_get_array_keys_filter($this->getAttribute((isset($context["settings"]) ? $context["settings"] : null), "menus", array())))) {
                // line 8
                echo "            ";
                $context["attr"] = " disabled=\"disabled\"";
                // line 9
                echo "        ";
            } else {
                // line 10
                echo "            ";
                $context["attr"] = "";
                // line 11
                echo "        ";
            }
            // line 12
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "term_id", array()), "html", null, true);
            echo "\"";
            echo twig_escape_filter($this->env, (isset($context["attr"]) ? $context["attr"] : null), "html", null, true);
            echo ">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "name", array()), "html", null, true);
            echo "</option>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['menu_key'], $context['menu'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 14
        echo "</select>
";
    }

    public function getTemplateName()
    {
        return "dropdown-menus.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 14,  56 => 12,  53 => 11,  50 => 10,  47 => 9,  44 => 8,  41 => 7,  38 => 6,  35 => 5,  31 => 4,  27 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<h4><label for=\"wpml-ls-available-menus\">{{ strings.menus.select_label }}:</label>  {% include 'tooltip.twig' with { \"content\": strings.tooltips.available_menus } %}</h4>
<select name=\"wpml_ls_available_menus\" class=\"js-wpml-ls-available-slots js-wpml-ls-available-menus\">
    <option disabled=\"disabled\">-- {{ strings.menus.select_option_choose }} --</option>
    {% for menu_key, menu in menus %}
        {% if menu_key == slug %}
            {% set attr = ' selected=\"selected\"' %}
        {% elseif menu.term_id in settings.menus|keys %}
            {% set attr = ' disabled=\"disabled\"' %}
        {% else %}
            {% set attr = '' %}
        {% endif %}
        <option value=\"{{ menu.term_id }}\"{{ attr }}>{{ menu.name }}</option>
    {% endfor %}
</select>
", "dropdown-menus.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/dropdown-menus.twig");
    }
}
