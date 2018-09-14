<?php

/* radio-position-menu.twig */
class __TwigTemplate_a7b4f67ec318e5b0072ae00e943f4acf47729c9a0c6a36a8be07898217410416 extends Twig_Template
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
        if ( !$this->getAttribute((isset($context["slot_settings"]) ? $context["slot_settings"] : null), "position_in_menu", array())) {
            // line 2
            echo "    ";
            $context["menu_position"] = "after";
        } else {
            // line 4
            echo "    ";
            $context["menu_position"] = $this->getAttribute((isset($context["slot_settings"]) ? $context["slot_settings"] : null), "position_in_menu", array());
        }
        // line 6
        echo "
<h4><label>";
        // line 7
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "menus", array()), "position_label", array()), "html", null, true);
        echo "</label>  ";
        $this->loadTemplate("tooltip.twig", "radio-position-menu.twig", 7)->display(array_merge($context, array("content" => $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "tooltips", array()), "menu_position", array()))));
        echo "</h4>
<ul>
    <li>
        <label>
            <input type=\"radio\" name=\"";
        // line 11
        if ((isset($context["name_base"]) ? $context["name_base"] : null)) {
            echo twig_escape_filter($this->env, (isset($context["name_base"]) ? $context["name_base"] : null), "html", null, true);
            echo "[position_in_menu]";
        } else {
            echo "position_in_menu";
        }
        echo "\"
                   class=\" js-wpml-ls-trigger-update\"
                   value=\"before\"";
        // line 13
        if (((isset($context["menu_position"]) ? $context["menu_position"] : null) == "before")) {
            echo " checked=\"checked\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "menus", array()), "position_first_item", array()), "html", null, true);
        echo "
        </label>
    </li>
    <li>
        <label>
            <input type=\"radio\" name=\"";
        // line 18
        if ((isset($context["name_base"]) ? $context["name_base"] : null)) {
            echo twig_escape_filter($this->env, (isset($context["name_base"]) ? $context["name_base"] : null), "html", null, true);
            echo "[position_in_menu]";
        } else {
            echo "position_in_menu";
        }
        echo "\"
                   class=\" js-wpml-ls-trigger-update\"
                   value=\"after\"";
        // line 20
        if (((isset($context["menu_position"]) ? $context["menu_position"] : null) == "after")) {
            echo " checked=\"checked\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "menus", array()), "position_last_item", array()), "html", null, true);
        echo "
        </label>
    </li>
</ul>";
    }

    public function getTemplateName()
    {
        return "radio-position-menu.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 20,  63 => 18,  51 => 13,  41 => 11,  32 => 7,  29 => 6,  25 => 4,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% if not slot_settings.position_in_menu %}
    {% set menu_position = 'after' %}
{% else %}
    {% set menu_position = slot_settings.position_in_menu %}
{% endif %}

<h4><label>{{ strings.menus.position_label }}</label>  {% include 'tooltip.twig' with { \"content\": strings.tooltips.menu_position } %}</h4>
<ul>
    <li>
        <label>
            <input type=\"radio\" name=\"{% if name_base %}{{ name_base }}[position_in_menu]{% else %}position_in_menu{% endif %}\"
                   class=\" js-wpml-ls-trigger-update\"
                   value=\"before\"{% if menu_position == 'before' %} checked=\"checked\"{% endif %}>{{ strings.menus.position_first_item }}
        </label>
    </li>
    <li>
        <label>
            <input type=\"radio\" name=\"{% if name_base %}{{ name_base }}[position_in_menu]{% else %}position_in_menu{% endif %}\"
                   class=\" js-wpml-ls-trigger-update\"
                   value=\"after\"{% if menu_position == 'after' %} checked=\"checked\"{% endif %}>{{ strings.menus.position_last_item }}
        </label>
    </li>
</ul>", "radio-position-menu.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/radio-position-menu.twig");
    }
}
