<?php

/* Skins/BoutiqueDefault-3.0.ENG/Modules/Boutique/Components/Bootstrap.Navigation/Default.twig */
class __TwigTemplate_f427fe84ff6f5608c4076ed280d6232fdf5dcbc0958393caead77d614219c69b extends Twig_Template
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
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["Menus"]) ? $context["Menus"] : null));
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
            // line 2
            echo "<div class=\"block\">
    <h3 class=\"title_block\">";
            // line 3
            echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Titre", array()), "html", null, true);
            echo "</h3>
    <div class=\"block_content\">
        <ul class=\"navigation\">
            ";
            // line 6
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["menu"], "getSubMenus", array(), "method"));
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
            foreach ($context['_seq'] as $context["_key"] => $context["menu2"]) {
                // line 7
                echo "            <li>
                ";
                // line 8
                if (preg_match((((("#^" . $this->getAttribute($context["menu"], "Url", array())) . "/") . $this->getAttribute($context["menu2"], "Url", array())) . ".*#"), (isset($context["Lien"]) ? $context["Lien"] : null))) {
                    // line 9
                    echo "                    <a href=\"/";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Url", array()), "html", null, true);
                    echo "/";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["menu2"], "Url", array()), "html", null, true);
                    echo "\" class=\"selected\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["menu2"], "Titre", array()), "html", null, true);
                    echo "</a>
                    ";
                    // line 10
                    echo twig_include($this->env, $context, twig_template_from_string($this->env, KeTwig::callComponent((((((("Boutique/Bootstrap.Navigation/SNavigation?Url=" . $this->getAttribute($context["menu"], "Url", array())) . "/") . $this->getAttribute($context["menu2"], "Url", array())) . "&CatId=") . $this->getAttribute($context["menu2"], "Id", array())) . "&Niveau=2"))));
                    echo "
                ";
                } else {
                    // line 12
                    echo "                    <a href=\"/";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["menu"], "Url", array()), "html", null, true);
                    echo "/";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["menu2"], "Url", array()), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["menu2"], "Titre", array()), "html", null, true);
                    echo "</a>
                ";
                }
                // line 14
                echo "            </li>
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['menu2'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 16
            echo "        </ul>
    </div>
</div>
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
    }

    public function getTemplateName()
    {
        return "Skins/BoutiqueDefault-3.0.ENG/Modules/Boutique/Components/Bootstrap.Navigation/Default.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  106 => 16,  91 => 14,  81 => 12,  76 => 10,  67 => 9,  65 => 8,  62 => 7,  45 => 6,  39 => 3,  36 => 2,  19 => 1,);
    }
}
/* {% for menu in Menus %}*/
/* <div class="block">*/
/*     <h3 class="title_block">{{ menu.Titre }}</h3>*/
/*     <div class="block_content">*/
/*         <ul class="navigation">*/
/*             {% for menu2 in menu.getSubMenus() %}*/
/*             <li>*/
/*                 {% if Lien matches '#^' ~ menu.Url ~ '/' ~ menu2.Url ~ '.*#' %}*/
/*                     <a href="/{{ menu.Url }}/{{ menu2.Url}}" class="selected">{{ menu2.Titre }}</a>*/
/*                     {{ include(template_from_string(component("Boutique/Bootstrap.Navigation/SNavigation?Url="~menu.Url~"/"~menu2.Url~"&CatId="~menu2.Id~"&Niveau=2"))) }}*/
/*                 {% else %}*/
/*                     <a href="/{{ menu.Url }}/{{ menu2.Url }}">{{ menu2.Titre }}</a>*/
/*                 {% endif %}*/
/*             </li>*/
/*             {% endfor %}*/
/*         </ul>*/
/*     </div>*/
/* </div>*/
/* {% endfor %}*/
