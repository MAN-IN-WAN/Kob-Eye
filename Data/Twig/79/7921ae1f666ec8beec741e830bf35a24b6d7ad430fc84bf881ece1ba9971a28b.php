<?php

/* Skins/TestTwig/Modules/Systeme/Header/Default.twig */
class __TwigTemplate_880033dae60f5022d163369e1ea42a161c84f891e098b71abd169dea10e6bb5f extends Twig_Template
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
        echo "<header id=\"header\" class=\"header-wrap\" style=\"background-image: url(/";
        echo twig_escape_filter($this->env, (isset($context["image"]) ? $context["image"] : null), "html", null, true);
        echo ");\">
    <section class=\"header\">
        <div class=\"container\" >
            <div class=\"row\">
                <div class=\"col-md-3\">
                    <a id=\"header_logo\" href=\"/\" title=\"";
        // line 6
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["CurrentMagasin"]) ? $context["CurrentMagasin"] : null), "Nom", array()), "html", null, true);
        echo "\"> <img class=\"logo img-responsive\" src=\"/";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["CurrentMagasin"]) ? $context["CurrentMagasin"] : null), "Logo", array()), "html", null, true);
        echo "\" alt=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["CurrentMagasin"]) ? $context["CurrentMagasin"] : null), "Nom", array()), "html", null, true);
        echo "\" /> </a>
                </div>
                <div class=\"col-md-9\">
                    <div class=\"row\" style=\"margin-bottom: 20px;\">
                        <div class=\"col-md-4  carre-wrapper\" style=\"padding:0;\">
                            <a class=\"carre carre-vert\" href=\"/Mon-compte\"><i class=\"fa fa-user-md\"></i><p>Mon compte</p></a>
                            <a class=\"carre carre-orange\" href=\"/Systeme/Deconnexion\"><i class=\"fa fa-sign-out\"></i><p>Se déconnecter</p></a>
                        </div>
                        <div class=\"col-md-3 \" style=\"padding:0;\">
                            ";
        // line 15
        echo twig_include($this->env, $context, twig_template_from_string($this->env, KeTwig::callModule("Systeme/Header/TopSearch")));
        echo "<br />
                            ";
        // line 16
        echo twig_include($this->env, $context, twig_template_from_string($this->env, KeTwig::callModule("Systeme/Header/BrandSearch")));
        echo "
                        </div>
                        <div class=\"col-md-5\" style=\"padding:0;text-align: right\">
                            <h1 style=\"margin-top: 10px;font-size: 24px;font-weight: 800;\">Bienvenue à la ";
        // line 19
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["CurrentMagasin"]) ? $context["CurrentMagasin"] : null), "Nom", array()), "html", null, true);
        echo ".</h1>
                            ";
        // line 20
        if (($this->getAttribute((isset($context["CurrentUser"]) ? $context["CurrentUser"] : null), "Public", array()) != true)) {
            // line 21
            echo "                                Bonjour <strong>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["CurrentUser"]) ? $context["CurrentUser"] : null), "Nom", array()), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["CurrentUser"]) ? $context["CurrentUser"] : null), "Prenom", array()), "html", null, true);
            echo "</strong>. Vous pouvez maintenant passer commande sur notre click and collect ou encore scanner votre ordonnance afin que nous la préparions en attendant votre arrivée.
                            ";
        } else {
            // line 23
            echo "                                <p>Consultez et préparez vos achats depuis chez vous et venez retirer vos commandes en officine sans attente.  </p>
                            ";
        }
        // line 25
        echo "                        </div>
                    </div>
                    ";
        // line 27
        echo twig_include($this->env, $context, twig_template_from_string($this->env, KeTwig::callComponent("Systeme/Bootstrap.MegaMenu")));
        echo "
                </div>
            </div>
        </div>
    </section>
</header>
";
    }

    public function getTemplateName()
    {
        return "Skins/TestTwig/Modules/Systeme/Header/Default.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  76 => 27,  72 => 25,  68 => 23,  60 => 21,  58 => 20,  54 => 19,  48 => 16,  44 => 15,  28 => 6,  19 => 1,);
    }
}
/* <header id="header" class="header-wrap" style="background-image: url(/{{ image }});">*/
/*     <section class="header">*/
/*         <div class="container" >*/
/*             <div class="row">*/
/*                 <div class="col-md-3">*/
/*                     <a id="header_logo" href="/" title="{{ CurrentMagasin.Nom }}"> <img class="logo img-responsive" src="/{{ CurrentMagasin.Logo }}" alt="{{ CurrentMagasin.Nom }}" /> </a>*/
/*                 </div>*/
/*                 <div class="col-md-9">*/
/*                     <div class="row" style="margin-bottom: 20px;">*/
/*                         <div class="col-md-4  carre-wrapper" style="padding:0;">*/
/*                             <a class="carre carre-vert" href="/Mon-compte"><i class="fa fa-user-md"></i><p>Mon compte</p></a>*/
/*                             <a class="carre carre-orange" href="/Systeme/Deconnexion"><i class="fa fa-sign-out"></i><p>Se déconnecter</p></a>*/
/*                         </div>*/
/*                         <div class="col-md-3 " style="padding:0;">*/
/*                             {{ include(template_from_string(module('Systeme/Header/TopSearch'))) }}<br />*/
/*                             {{ include(template_from_string(module('Systeme/Header/BrandSearch'))) }}*/
/*                         </div>*/
/*                         <div class="col-md-5" style="padding:0;text-align: right">*/
/*                             <h1 style="margin-top: 10px;font-size: 24px;font-weight: 800;">Bienvenue à la {{ CurrentMagasin.Nom }}.</h1>*/
/*                             {% if CurrentUser.Public != true %}*/
/*                                 Bonjour <strong>{{ CurrentUser.Nom }} {{ CurrentUser.Prenom }}</strong>. Vous pouvez maintenant passer commande sur notre click and collect ou encore scanner votre ordonnance afin que nous la préparions en attendant votre arrivée.*/
/*                             {% else %}*/
/*                                 <p>Consultez et préparez vos achats depuis chez vous et venez retirer vos commandes en officine sans attente.  </p>*/
/*                             {% endif %}*/
/*                         </div>*/
/*                     </div>*/
/*                     {{ include(template_from_string(component('Systeme/Bootstrap.MegaMenu'))) }}*/
/*                 </div>*/
/*             </div>*/
/*         </div>*/
/*     </section>*/
/* </header>*/
/* */
