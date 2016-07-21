<?php

/* Skins/BoutiqueDefault-3.0.ENG/Modules/Systeme/Footer/BottomMenu.twig */
class __TwigTemplate_412924882bf424251c683ddc651cc2bfaf9a5438a9faf90b5bec883e17ce1502 extends Twig_Template
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
        echo "<div class=\"lof-block-wrap\">
    ";
        // line 2
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["menus"]) ? $context["menus"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["menu"]) {
            // line 3
            echo "    <h3><a href=\"/";
            echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Url", array()), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Description", array()), "html", null, true);
            echo "\" rel=\"nofollow\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Titre", array()), "html", null, true);
            echo "</a></h3>
    <ul class=\"bullet\">
        ";
            // line 5
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["menu"], "getSubMenus", array(0 => 10), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["menu2"]) {
                // line 6
                echo "        <li class=\"item\">
            <a href=\"/";
                // line 7
                echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Url", array()), "html", null, true);
                echo "/";
                echo twig_escape_filter($this->env, $this->getAttribute($context["menu2"], "Url", array()), "html", null, true);
                echo "\" title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["menu2"], "Description", array()), "html", null, true);
                echo "\" rel=\"nofollow\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["menu2"], "Titre", array()), "html", null, true);
                echo "</a>
        </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['menu2'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 10
            echo "    </ul>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['menu'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 12
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "Skins/BoutiqueDefault-3.0.ENG/Modules/Systeme/Footer/BottomMenu.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  65 => 12,  58 => 10,  43 => 7,  40 => 6,  36 => 5,  26 => 3,  22 => 2,  19 => 1,);
    }
}
/* <div class="lof-block-wrap">*/
/*     {% for menu in menus %}*/
/*     <h3><a href="/{{ menu.Url }}" title="{{ menu.Description }}" rel="nofollow">{{ menu.Titre }}</a></h3>*/
/*     <ul class="bullet">*/
/*         {% for menu2 in menu.getSubMenus(10) %}*/
/*         <li class="item">*/
/*             <a href="/{{ menu.Url }}/{{ menu2.Url }}" title="{{ menu2.Description }}" rel="nofollow">{{ menu2.Titre }}</a>*/
/*         </li>*/
/*         {% endfor %}*/
/*     </ul>*/
/*     {% endfor %}*/
/* </div>*/
