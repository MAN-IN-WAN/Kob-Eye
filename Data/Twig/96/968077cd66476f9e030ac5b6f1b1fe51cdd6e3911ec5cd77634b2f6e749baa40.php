<?php

/* Skins/BoutiqueDefault-3.0.ENG/Modules/Boutique/Components/Bootstrap.Navigation/SNavigation.twig */
class __TwigTemplate_5dc4b47d3939580c850dc9c109af8a91a25d02ace0d9211259fa6ac1d5661158 extends Twig_Template
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
        if ((twig_length_filter($this->env, (isset($context["menus"]) ? $context["menus"] : null)) > 0)) {
            // line 2
            echo "<ul>
    ";
            // line 3
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["menus"]) ? $context["menus"] : null));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["menu"]) {
                // line 4
                echo "    <li>
        <a href=\"/";
                // line 5
                echo twig_escape_filter($this->env, (isset($context["Url"]) ? $context["Url"] : null), "html", null, true);
                echo "/";
                echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Url", array()), "html", null, true);
                echo "\" ";
                if (preg_match((((("#^" . (isset($context["Url"]) ? $context["Url"] : null)) . "/") . $this->getAttribute($context["menu"], "Url", array())) . ".*#"), (isset($context["Lien"]) ? $context["Lien"] : null))) {
                    echo "class=\"selected\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Nom", array()), "html", null, true);
                    echo "</a>
        ";
                    // line 6
                    echo twig_include($this->env, $context, twig_template_from_string($this->env, KeTwig::callComponent(((((((("Boutique/Bootstrap.Navigation/SNavigation?Url=" . (isset($context["Url"]) ? $context["Url"] : null)) . "/") . $this->getAttribute($context["menu"], "Url", array())) . "&CatId=") . $this->getAttribute($context["menu"], "Id", array())) . "&Niveau=") . ((isset($context["Niveau"]) ? $context["Niveau"] : null) + 1)))));
                    echo "
        ";
                } else {
                    // line 8
                    echo "            >";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Nom", array()), "html", null, true);
                    echo "</a>
        ";
                }
                // line 10
                echo "    </li>
    ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['menu'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 12
            echo "</ul>
";
        }
    }

    public function getTemplateName()
    {
        return "Skins/BoutiqueDefault-3.0.ENG/Modules/Boutique/Components/Bootstrap.Navigation/SNavigation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  80 => 12,  65 => 10,  59 => 8,  54 => 6,  44 => 5,  41 => 4,  24 => 3,  21 => 2,  19 => 1,);
    }
}
/* {% if menus|length > 0 %}*/
/* <ul>*/
/*     {% for menu in menus %}*/
/*     <li>*/
/*         <a href="/{{ Url }}/{{ menu.Url }}" {% if Lien matches '#^' ~ Url ~ '\/' ~ menu.Url ~ '.*#' %}class="selected">{{ menu.Nom }}</a>*/
/*         {{ include(template_from_string(component("Boutique/Bootstrap.Navigation/SNavigation?Url="~Url~"/"~menu.Url~"&CatId="~menu.Id~"&Niveau="~(Niveau+1)))) }}*/
/*         {% else %}*/
/*             >{{ menu.Nom }}</a>*/
/*         {% endif %}*/
/*     </li>*/
/*     {% endfor %}*/
/* </ul>*/
/* {% endif %}*/
