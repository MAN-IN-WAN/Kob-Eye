<?php

/* Skins/TestTwig/Modules/Systeme/Header/BrandSearch.twig */
class __TwigTemplate_764f14230723002fb76494d0b8aa7734949b23c38ce7da5bc8b8c367889b646b extends Twig_Template
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
        echo "
<div class=\"btn-group\" style=\"width:100%\">
    <button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" style=\"width:100%\">
        Marques... <span class=\"caret\"></span>
    </button>
    <ul class=\"dropdown-menu\">
        ";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["marques"]) ? $context["marques"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["marque"]) {
            // line 8
            echo "        <li><a href=\"/";
            echo twig_escape_filter($this->env, $this->getAttribute($context["marque"], "Url", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["marque"], "Nom", array()), "html", null, true);
            echo "</a></li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['marque'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 10
        echo "    </ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "Skins/TestTwig/Modules/Systeme/Header/BrandSearch.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 10,  31 => 8,  27 => 7,  19 => 1,);
    }
}
/* */
/* <div class="btn-group" style="width:100%">*/
/*     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width:100%">*/
/*         Marques... <span class="caret"></span>*/
/*     </button>*/
/*     <ul class="dropdown-menu">*/
/*         {% for marque in marques %}*/
/*         <li><a href="/{{ marque.Url }}">{{ marque.Nom }}</a></li>*/
/*         {% endfor %}*/
/*     </ul>*/
/* </div>*/
/* */
