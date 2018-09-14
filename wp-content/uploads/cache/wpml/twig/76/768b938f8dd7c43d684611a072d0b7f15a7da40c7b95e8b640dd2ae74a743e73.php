<?php

/* template.twig */
class __TwigTemplate_aa65d0632151b7f23da72ad1eecb2483a8148b9b95dad6db240c22e287dd5012 extends Twig_Template
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
        $context["css_classes_flag"] = twig_trim_filter(("wpml-ls-flag " . $this->getAttribute((isset($context["backward_compatibility"]) ? $context["backward_compatibility"] : null), "css_classes_flag", array())));
        // line 2
        $context["css_classes_native"] = twig_trim_filter(("wpml-ls-native " . $this->getAttribute((isset($context["backward_compatibility"]) ? $context["backward_compatibility"] : null), "css_classes_native", array())));
        // line 3
        $context["css_classes_display"] = twig_trim_filter(("wpml-ls-display " . $this->getAttribute((isset($context["backward_compatibility"]) ? $context["backward_compatibility"] : null), "css_classes_display", array())));
        // line 4
        $context["css_classes_bracket"] = twig_trim_filter(("wpml-ls-bracket " . $this->getAttribute((isset($context["backward_compatibility"]) ? $context["backward_compatibility"] : null), "css_classes_bracket", array())));
        // line 5
        echo "
";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["languages"]) ? $context["languages"] : null));
        foreach ($context['_seq'] as $context["code"] => $context["language"]) {
            // line 7
            echo "    ";
            ob_start();
            // line 8
            echo "    <span class=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "css_classes", array()), "html", null, true);
            echo " wpml-ls-item-legacy-post-translations\">
        <a href=\"";
            // line 9
            echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "url", array()), "html", null, true);
            echo "\"";
            if ($this->getAttribute($this->getAttribute($context["language"], "backward_compatibility", array()), "css_classes_a", array())) {
                echo " class=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["language"], "backward_compatibility", array()), "css_classes_a", array()), "html", null, true);
                echo "\"";
            }
            echo ">";
            // line 10
            if ($this->getAttribute($context["language"], "flag_url", array())) {
                // line 11
                echo "<img class=\"";
                echo twig_escape_filter($this->env, (isset($context["css_classes_flag"]) ? $context["css_classes_flag"] : null), "html", null, true);
                echo "\" src=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "flag_url", array()), "html", null, true);
                echo "\" alt=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", array()), "html", null, true);
                echo "\" title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "flag_title", array()), "html", null, true);
                echo "\">";
            }
            // line 14
            if (($this->getAttribute($context["language"], "is_current", array()) && ($this->getAttribute($context["language"], "native_name", array()) || $this->getAttribute($context["language"], "display_name", array())))) {
                // line 16
                $context["current_language_name"] = (($this->getAttribute($context["language"], "native_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($context["language"], "native_name", array()), $this->getAttribute($context["language"], "display_name", array()))) : ($this->getAttribute($context["language"], "display_name", array())));
                // line 17
                echo "<span class=\"";
                echo twig_escape_filter($this->env, (isset($context["css_classes_native"]) ? $context["css_classes_native"] : null), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, (isset($context["current_language_name"]) ? $context["current_language_name"] : null), "html", null, true);
                echo "</span>";
            } else {
                // line 21
                if ($this->getAttribute($context["language"], "native_name", array())) {
                    // line 22
                    echo "<span class=\"";
                    echo twig_escape_filter($this->env, (isset($context["css_classes_native"]) ? $context["css_classes_native"] : null), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "native_name", array()), "html", null, true);
                    echo "</span>";
                }
                // line 25
                if ($this->getAttribute($context["language"], "display_name", array())) {
                    // line 26
                    echo "<span class=\"";
                    echo twig_escape_filter($this->env, (isset($context["css_classes_display"]) ? $context["css_classes_display"] : null), "html", null, true);
                    echo "\">";
                    // line 27
                    if ($this->getAttribute($context["language"], "native_name", array())) {
                        echo "<span class=\"";
                        echo twig_escape_filter($this->env, (isset($context["css_classes_bracket"]) ? $context["css_classes_bracket"] : null), "html", null, true);
                        echo "\"> (</span>";
                    }
                    // line 28
                    echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "display_name", array()), "html", null, true);
                    // line 29
                    if ($this->getAttribute($context["language"], "native_name", array())) {
                        echo "<span class=\"";
                        echo twig_escape_filter($this->env, (isset($context["css_classes_bracket"]) ? $context["css_classes_bracket"] : null), "html", null, true);
                        echo "\">)</span>";
                    }
                    // line 30
                    echo "</span>";
                }
            }
            // line 34
            echo "</a>
    </span>
    ";
            echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['code'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "template.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  108 => 34,  104 => 30,  98 => 29,  96 => 28,  90 => 27,  86 => 26,  84 => 25,  77 => 22,  75 => 21,  68 => 17,  66 => 16,  64 => 14,  53 => 11,  51 => 10,  42 => 9,  37 => 8,  34 => 7,  30 => 6,  27 => 5,  25 => 4,  23 => 3,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% set css_classes_flag    = ('wpml-ls-flag ' ~ backward_compatibility.css_classes_flag)|trim %}
{% set css_classes_native  = ('wpml-ls-native ' ~ backward_compatibility.css_classes_native)|trim %}
{% set css_classes_display = ('wpml-ls-display ' ~ backward_compatibility.css_classes_display)|trim %}
{% set css_classes_bracket = ('wpml-ls-bracket ' ~ backward_compatibility.css_classes_bracket)|trim %}

{% for code, language in languages %}
    {% spaceless %}
    <span class=\"{{ language.css_classes }} wpml-ls-item-legacy-post-translations\">
        <a href=\"{{ language.url }}\"{% if language.backward_compatibility.css_classes_a %} class=\"{{ language.backward_compatibility.css_classes_a }}\"{% endif %}>
            {%- if language.flag_url -%}
                <img class=\"{{ css_classes_flag }}\" src=\"{{ language.flag_url }}\" alt=\"{{ language.code }}\" title=\"{{ language.flag_title }}\">
            {%- endif -%}

            {%- if language.is_current and (language.native_name or language.display_name)  -%}

                {%- set current_language_name = language.native_name|default(language.display_name) -%}
                <span class=\"{{ css_classes_native }}\">{{- current_language_name -}}</span>

            {%- else -%}

                {%- if language.native_name -%}
                    <span class=\"{{ css_classes_native }}\">{{- language.native_name -}}</span>
                {%- endif -%}

                {%- if language.display_name -%}
                    <span class=\"{{ css_classes_display }}\">
                    {%- if language.native_name -%}<span class=\"{{ css_classes_bracket }}\"> (</span>{%- endif -%}
                        {{- language.display_name -}}
                        {%- if language.native_name -%}<span class=\"{{ css_classes_bracket }}\">)</span>{%- endif -%}
                    </span>
                {%- endif -%}

            {%- endif -%}
        </a>
    </span>
    {% endspaceless %}
{%- endfor -%}", "template.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switchers/legacy-post-translations/template.twig");
    }
}
