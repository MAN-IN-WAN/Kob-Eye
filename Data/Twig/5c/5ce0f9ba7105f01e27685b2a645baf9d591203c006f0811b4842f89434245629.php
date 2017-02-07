<?php

/* Skins/TestTwig/Modules/Systeme/Components/Bootstrap.MegaMenu/Default.twig */
class __TwigTemplate_bbab711b5d604472da417cd17da1bae065cc5f4a970a30ec16aa3d78874c7675 extends Twig_Template
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
        echo "<script type=\"text/javascript\" src=\"/Tools/Js/Masonry/masonry.min.js\"></script>
<nav id=\"topnavigation\" class=\"navbar yamm navbar-default \">
    <div class=\"container-fluid\">
        <div class=\"navbar-header\">
            <a data-target=\".navbar-collapse\" data-toggle=\"collapse\" class=\"btn btn-navbar\"> <span class=\"icon-bar\"></span> <span class=\"icon-bar\"></span> <span class=\"icon-bar\"></span> </a>
            <div class=\"navbar-collapse collapse\">
                <ul class=\"nav navbar-nav carre-wrapper\">
                ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["MainMenus"]) ? $context["MainMenus"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["men"]) {
            // line 9
            echo "                    ";
            if ($this->getAttribute($context["men"], "getSubMenus", array(), "method")) {
                // line 10
                echo "                        <li class=\"parent dropdown ";
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Couleur", array()), "html", null, true);
                echo "\">
                            <a class=\"dropdown-toggle carre carre-";
                // line 11
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Couleur", array()), "html", null, true);
                echo "\" data-toggle=\"dropdown\" href=\"/";
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Url", array()), "html", null, true);
                echo "\" onmouseover='\$(\"#container";
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Id", array()), "html", null, true);
                echo "\").masonry({ \"columnWidth\": 250, \"itemSelector\": \".item-menu\" });'><i class=\"fa fa-";
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "ClassCss", array()), "html", null, true);
                echo "\"></i><p>";
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Titre", array()), "html", null, true);
                echo "</p><b class=\"caret\"></b></a>
                            <div class=\"dropdown-menu menu-content mega-cols cols3\" ";
                // line 12
                if ($this->getAttribute($context["men"], "BackgroundImage", array())) {
                    echo "style=\"background-image:url(/";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "BackgroundImage", array()), "html", null, true);
                    echo ")\"";
                }
                echo ">
                                <div class=\"row\">
                                    <div id=\"container";
                // line 14
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Id", array()), "html", null, true);
                echo "\" class=\"span";
                if ($this->getAttribute($context["men"], "Publicites", array())) {
                    echo "9";
                } else {
                    echo "12";
                }
                echo "\"  style=\"position:relative;\">
                                        <ul class=\"level0 \">
                                        ";
                // line 16
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["men"], "getSubMenus", array(), "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["men2"]) {
                    // line 17
                    echo "                                            <li class=\"item-menu\" style=\"\">
                                                <a class=\"\" href=\"/";
                    // line 18
                    echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Url", array()), "html", null, true);
                    echo "/";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["men2"], "Url", array()), "html", null, true);
                    echo "\"><span class=\"menu-title\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["men2"], "Titre", array()), "html", null, true);
                    echo "</span></a>
                                                <ul class=\"level1\">
                                                    ";
                    // line 20
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["men2"], "getSubMenus", array(), "method"));
                    foreach ($context['_seq'] as $context["_key"] => $context["men3"]) {
                        // line 21
                        echo "                                                    <li class=\" \">
                                                        <a href=\"/";
                        // line 22
                        echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Url", array()), "html", null, true);
                        echo "/";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["men2"], "Url", array()), "html", null, true);
                        echo "/";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["men3"], "Url", array()), "html", null, true);
                        echo "\"><span class=\"menu-title\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["men3"], "Titre", array()), "html", null, true);
                        echo "</span></a>
                                                    </li>
                                                    ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['men3'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 25
                    echo "                                                </ul>
                                            </li>
                                         ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['men2'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 28
                echo "                                        </ul>
                                    </div>
                                    ";
                // line 30
                if ($this->getAttribute($context["men"], "Publicites", array())) {
                    // line 31
                    echo "                                    <div class=\"span3\">
                                        ";
                    // line 32
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["men"], "Publicites", array()));
                    foreach ($context['_seq'] as $context["_key"] => $context["pub"]) {
                        // line 33
                        echo "                                        <a href=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["pub"], "Alternatif", array()), "html", null, true);
                        echo "\"><img src=\"/";
                        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["pup"]) ? $context["pup"] : null), "Lien", array()), "html", null, true);
                        echo "\" width=\"300\" height=\"300\"/></a>
                                        ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['pub'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 35
                    echo "                                    </div>
                                    ";
                }
                // line 37
                echo "                                </div>
                            </div>
                        </li>
                    ";
            } else {
                // line 41
                echo "                        <li class=\"\">
                            <a href=\"/";
                // line 42
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Url", array()), "html", null, true);
                echo "\" class=\"carre carre-";
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "Couleur", array()), "html", null, true);
                echo "\"><i class=\"fa fa-";
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "ClassCss", array()), "html", null, true);
                echo "\"></i><p>";
                echo twig_escape_filter($this->env, $this->getAttribute($context["men"], "getFirstSearchOrder", array(), "method"), "html", null, true);
                echo "</p></a>
                        </li>
                    ";
            }
            // line 45
            echo "                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['men'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        echo "            </ul>
        </div>
    </div>
    </div>
</nav>
";
    }

    public function getTemplateName()
    {
        return "Skins/TestTwig/Modules/Systeme/Components/Bootstrap.MegaMenu/Default.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  173 => 46,  167 => 45,  155 => 42,  152 => 41,  146 => 37,  142 => 35,  131 => 33,  127 => 32,  124 => 31,  122 => 30,  118 => 28,  110 => 25,  95 => 22,  92 => 21,  88 => 20,  79 => 18,  76 => 17,  72 => 16,  61 => 14,  52 => 12,  40 => 11,  35 => 10,  32 => 9,  28 => 8,  19 => 1,);
    }
}
/* <script type="text/javascript" src="/Tools/Js/Masonry/masonry.min.js"></script>*/
/* <nav id="topnavigation" class="navbar yamm navbar-default ">*/
/*     <div class="container-fluid">*/
/*         <div class="navbar-header">*/
/*             <a data-target=".navbar-collapse" data-toggle="collapse" class="btn btn-navbar"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>*/
/*             <div class="navbar-collapse collapse">*/
/*                 <ul class="nav navbar-nav carre-wrapper">*/
/*                 {% for men in MainMenus %}*/
/*                     {% if men.getSubMenus() %}*/
/*                         <li class="parent dropdown {{ men.Couleur }}">*/
/*                             <a class="dropdown-toggle carre carre-{{ men.Couleur }}" data-toggle="dropdown" href="/{{ men.Url }}" onmouseover='$("#container{{ men.Id }}").masonry({ "columnWidth": 250, "itemSelector": ".item-menu" });'><i class="fa fa-{{ men.ClassCss }}"></i><p>{{ men.Titre }}</p><b class="caret"></b></a>*/
/*                             <div class="dropdown-menu menu-content mega-cols cols3" {% if men.BackgroundImage %}style="background-image:url(/{{ men.BackgroundImage }})"{% endif %}>*/
/*                                 <div class="row">*/
/*                                     <div id="container{{ men.Id }}" class="span{% if men.Publicites %}9{% else %}12{% endif %}"  style="position:relative;">*/
/*                                         <ul class="level0 ">*/
/*                                         {% for men2 in men.getSubMenus() %}*/
/*                                             <li class="item-menu" style="">*/
/*                                                 <a class="" href="/{{ men.Url }}/{{ men2.Url }}"><span class="menu-title">{{ men2.Titre }}</span></a>*/
/*                                                 <ul class="level1">*/
/*                                                     {% for men3 in men2.getSubMenus() %}*/
/*                                                     <li class=" ">*/
/*                                                         <a href="/{{ men.Url }}/{{ men2.Url }}/{{ men3.Url }}"><span class="menu-title">{{ men3.Titre }}</span></a>*/
/*                                                     </li>*/
/*                                                     {% endfor %}*/
/*                                                 </ul>*/
/*                                             </li>*/
/*                                          {% endfor %}*/
/*                                         </ul>*/
/*                                     </div>*/
/*                                     {% if men.Publicites %}*/
/*                                     <div class="span3">*/
/*                                         {% for pub in men.Publicites %}*/
/*                                         <a href="{{ pub.Alternatif }}"><img src="/{{ pup.Lien }}" width="300" height="300"/></a>*/
/*                                         {% endfor %}*/
/*                                     </div>*/
/*                                     {% endif %}*/
/*                                 </div>*/
/*                             </div>*/
/*                         </li>*/
/*                     {% else %}*/
/*                         <li class="">*/
/*                             <a href="/{{ men.Url }}" class="carre carre-{{ men.Couleur }}"><i class="fa fa-{{ men.ClassCss }}"></i><p>{{ men.getFirstSearchOrder() }}</p></a>*/
/*                         </li>*/
/*                     {% endif %}*/
/*                 {% endfor %}*/
/*             </ul>*/
/*         </div>*/
/*     </div>*/
/*     </div>*/
/* </nav>*/
/* */
