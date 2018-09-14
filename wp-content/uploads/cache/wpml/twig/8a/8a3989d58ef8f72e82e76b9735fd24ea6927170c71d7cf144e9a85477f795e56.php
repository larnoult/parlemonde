<?php

/* template.twig */
class __TwigTemplate_f2fa05de6dfd60f81b53b97af6c8140fe1aa7a39d83012be651a57fb664eab0e extends Twig_Template
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
<div class=\"";
        // line 6
        echo twig_escape_filter($this->env, (isset($context["css_classes"]) ? $context["css_classes"] : null), "html", null, true);
        echo " wpml-ls-legacy-list-horizontal\"";
        if ($this->getAttribute((isset($context["backward_compatibility"]) ? $context["backward_compatibility"] : null), "css_id", array())) {
            echo " id=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["backward_compatibility"]) ? $context["backward_compatibility"] : null), "css_id", array()), "html", null, true);
            echo "\"";
        }
        echo ">
\t<ul>";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["languages"]) ? $context["languages"] : null));
        foreach ($context['_seq'] as $context["code"] => $context["language"]) {
            // line 10
            echo "<li class=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "css_classes", array()), "html", null, true);
            echo " wpml-ls-item-legacy-list-horizontal\">
\t\t\t\t<a href=\"";
            // line 11
            echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "url", array()), "html", null, true);
            echo "\"";
            if ($this->getAttribute($this->getAttribute($context["language"], "backward_compatibility", array()), "css_classes_a", array())) {
                echo " class=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["language"], "backward_compatibility", array()), "css_classes_a", array()), "html", null, true);
                echo "\"";
            }
            echo ">";
            // line 12
            if ($this->getAttribute($context["language"], "flag_url", array())) {
                // line 13
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
            // line 16
            if (($this->getAttribute($context["language"], "is_current", array()) && ($this->getAttribute($context["language"], "native_name", array()) || $this->getAttribute($context["language"], "display_name", array())))) {
                // line 18
                $context["current_language_name"] = (($this->getAttribute($context["language"], "native_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($context["language"], "native_name", array()), $this->getAttribute($context["language"], "display_name", array()))) : ($this->getAttribute($context["language"], "display_name", array())));
                // line 19
                echo "<span class=\"";
                echo twig_escape_filter($this->env, (isset($context["css_classes_native"]) ? $context["css_classes_native"] : null), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, (isset($context["current_language_name"]) ? $context["current_language_name"] : null), "html", null, true);
                echo "</span>";
            } else {
                // line 23
                if ($this->getAttribute($context["language"], "native_name", array())) {
                    // line 24
                    echo "<span class=\"";
                    echo twig_escape_filter($this->env, (isset($context["css_classes_native"]) ? $context["css_classes_native"] : null), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "native_name", array()), "html", null, true);
                    echo "</span>";
                }
                // line 27
                if ($this->getAttribute($context["language"], "display_name", array())) {
                    // line 28
                    echo "<span class=\"";
                    echo twig_escape_filter($this->env, (isset($context["css_classes_display"]) ? $context["css_classes_display"] : null), "html", null, true);
                    echo "\">";
                    // line 29
                    if ($this->getAttribute($context["language"], "native_name", array())) {
                        echo "<span class=\"";
                        echo twig_escape_filter($this->env, (isset($context["css_classes_bracket"]) ? $context["css_classes_bracket"] : null), "html", null, true);
                        echo "\"> (</span>";
                    }
                    // line 30
                    echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "display_name", array()), "html", null, true);
                    // line 31
                    if ($this->getAttribute($context["language"], "native_name", array())) {
                        echo "<span class=\"";
                        echo twig_escape_filter($this->env, (isset($context["css_classes_bracket"]) ? $context["css_classes_bracket"] : null), "html", null, true);
                        echo "\">)</span>";
                    }
                    // line 32
                    echo "</span>";
                }
            }
            // line 36
            echo "</a>
\t\t\t</li>";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['code'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 40
        echo "</ul>
</div>";
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
        return array (  122 => 40,  115 => 36,  111 => 32,  105 => 31,  103 => 30,  97 => 29,  93 => 28,  91 => 27,  84 => 24,  82 => 23,  75 => 19,  73 => 18,  71 => 16,  60 => 13,  58 => 12,  49 => 11,  44 => 10,  40 => 9,  30 => 6,  27 => 5,  25 => 4,  23 => 3,  21 => 2,  19 => 1,);
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

<div class=\"{{ css_classes }} wpml-ls-legacy-list-horizontal\"{% if backward_compatibility.css_id %} id=\"{{ backward_compatibility.css_id }}\"{% endif %}>
\t<ul>

\t\t{%- for code, language in languages -%}
\t\t\t<li class=\"{{ language.css_classes }} wpml-ls-item-legacy-list-horizontal\">
\t\t\t\t<a href=\"{{ language.url }}\"{% if language.backward_compatibility.css_classes_a %} class=\"{{ language.backward_compatibility.css_classes_a }}\"{% endif %}>
\t\t\t\t\t{%- if language.flag_url -%}
\t\t\t\t\t\t<img class=\"{{ css_classes_flag }}\" src=\"{{ language.flag_url }}\" alt=\"{{ language.code }}\" title=\"{{ language.flag_title }}\">
\t\t\t\t\t{%- endif -%}

\t\t\t\t\t{%- if language.is_current and (language.native_name or language.display_name)  -%}

\t\t\t\t\t\t{%- set current_language_name = language.native_name|default(language.display_name) -%}
\t\t\t\t\t\t<span class=\"{{ css_classes_native }}\">{{- current_language_name -}}</span>

\t\t\t\t\t{%- else -%}

\t\t\t\t\t\t{%- if language.native_name -%}
\t\t\t\t\t\t\t<span class=\"{{ css_classes_native }}\">{{- language.native_name -}}</span>
\t\t\t\t\t\t{%- endif -%}

\t\t\t\t\t\t{%- if language.display_name -%}
\t\t\t\t\t\t\t<span class=\"{{ css_classes_display }}\">
\t\t\t\t\t\t\t{%- if language.native_name -%}<span class=\"{{ css_classes_bracket }}\"> (</span>{%- endif -%}
\t\t\t\t\t\t\t\t{{- language.display_name -}}
\t\t\t\t\t\t\t{%- if language.native_name -%}<span class=\"{{ css_classes_bracket }}\">)</span>{%- endif -%}
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t{%- endif -%}

\t\t\t\t\t{%- endif -%}
\t\t\t\t</a>
\t\t\t</li>
\t\t{%- endfor -%}

\t</ul>
</div>", "template.twig", "/home/parlemon/www/wp-content/plugins/sitepress-multilingual-cms/templates/language-switchers/legacy-list-horizontal/template.twig");
    }
}
