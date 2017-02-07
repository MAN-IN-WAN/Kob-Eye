<?php

/* Skins/BoutiqueDefault-3.0.ENG/Modules/Boutique/Components/Bootstrap.ProduitCoupDeCoeur/Default.twig */
class __TwigTemplate_6c64a76155f652388abaae0fef6451da3b50e6a58e5cea7214a00a1668e34c33 extends Twig_Template
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
        echo "<!-- MODULE Block specials -->

<div id=\"categoriesprodtabs\" class=\"block products_block exclusive blockleocategoriestabs\">
    <h3 class=\"title_block\">";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["TITRE"]) ? $context["TITRE"] : null), "html", null, true);
        echo "</h3>
    <div class=\"block_content\">
        <!-- Products list -->
        <div id=\"product_list\" class=\"products_block view-grid\">
            <div class=\"row\">
                ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["prods"]) ? $context["prods"] : null));
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
        foreach ($context['_seq'] as $context["_key"] => $context["prod"]) {
            // line 10
            echo "                <!-- Product item -->
                <div class=\"p-item col-md-4 product_block ajax_block_product ";
            // line 11
            if ($this->getAttribute($context["loop"], "first", array())) {
                echo "first_item";
            }
            if ($this->getAttribute($context["loop"], "last", array())) {
                echo "last_item";
            }
            echo " ";
            if ((($this->getAttribute($context["loop"], "index", array()) % 2) == 0)) {
                echo " alternate_item";
            } else {
                echo " item";
            }
            echo "  \">
                    <div class=\"list-products\">
                        <div class=\"product-container clearfix\">
                            <div class=\"center_block\">
                                <a href=\"";
            // line 15
            echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Url", array()), "html", null, true);
            echo "\" class=\"product_img_link\" title=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Nom", array()), "html", null, true);
            echo "\"> <img src=\"/";
            if (($this->getAttribute($context["prod"], "Image", array()) != "")) {
                echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Image", array()), "html", null, true);
                echo " ";
            } else {
                echo "Skins/[!Systeme::Skin!]/Img/image_def.jpg";
            }
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Nom", array()), "html", null, true);
            echo "\" class=\"img-responsive\" style=\"max-height:200px;margin: auto;\" /> <span class=\"new\">__NEW__</span> </a>
                                ";
            // line 16
            if ($this->getAttribute($context["prod"], "Promo", array())) {
                // line 17
                echo "                                <span class=\"discount\">__PROMO__</span>
                                ";
            }
            // line 19
            echo "
                            </div>
                            <div class=\"right_block\">
                                <h3 class=\"s_title_block\"><a href=\"";
            // line 22
            echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Url", array()), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Nom", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Nom", array()), "html", null, true);
            echo "</a></h3>

                                <div class=\"price_container\">
                                    </span>
                                    ";
            // line 26
            if ($this->getAttribute($context["prod"], "Promo", array())) {
                // line 27
                echo "                                    <div style=\"display:block;color:#fff;font-size:13px;position:absolute;right:32px;text-decoration:line-through;top:0;\" id=\"tarifNonPromo\">
                                        ";
                // line 28
                echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "getTarifHorsPromo", array(), "method"), "html", null, true);
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["CurrentDevise"]) ? $context["CurrentDevise"] : null), "Sigle", array()), "html", null, true);
                echo "
                                    </div>
                                    ";
            }
            // line 31
            echo "                                    ";
            if ($this->getAttribute($context["prod"], "MultiTarif", array())) {
                echo "<span class=\"BlocProduitApartir\">__A_PARTIR_DE__</span>";
            }
            echo " <span class=\"price\" style=\"display: inline;\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Prix", array()), "html", null, true);
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["CurrentDevise"]) ? $context["CurrentDevise"] : null), "Sigle", array()), "html", null, true);
            echo "</span>
                                    <br />
                                    ";
            // line 33
            if ($this->getAttribute($context["prod"], "StockReference", array())) {
                // line 34
                echo "                                    <span class=\"availability\">__AVAILABLE__</span>
                                    ";
            }
            // line 36
            echo "                                </div>
                                <span class=\"online_only\"></span>

                                <a class=\"btn btn-success\" href=\"";
            // line 39
            echo twig_escape_filter($this->env, $this->getAttribute($context["prod"], "Url", array()), "html", null, true);
            echo "\" title=\"__ADD_TO_CART__\">__ADD_TO_CART__</a>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Product item -->
                ";
            // line 46
            if (((($this->getAttribute($context["loop"], "index", array()) % 3) == 0) && ($this->getAttribute($context["loop"], "last", array()) == 0))) {
                // line 47
                echo "            </div>
            <div class=\"row\">
                ";
            }
            // line 50
            echo "                ";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['prod'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 51
        echo "
            </div>
        </div>
        <!-- /Products list -->
    </div>
</div>
<!-- /MODULE Block specials -->

";
    }

    public function getTemplateName()
    {
        return "Skins/BoutiqueDefault-3.0.ENG/Modules/Boutique/Components/Bootstrap.ProduitCoupDeCoeur/Default.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  172 => 51,  158 => 50,  153 => 47,  151 => 46,  141 => 39,  136 => 36,  132 => 34,  130 => 33,  119 => 31,  112 => 28,  109 => 27,  107 => 26,  96 => 22,  91 => 19,  87 => 17,  85 => 16,  70 => 15,  52 => 11,  49 => 10,  32 => 9,  24 => 4,  19 => 1,);
    }
}
/* <!-- MODULE Block specials -->*/
/* */
/* <div id="categoriesprodtabs" class="block products_block exclusive blockleocategoriestabs">*/
/*     <h3 class="title_block">{{ TITRE }}</h3>*/
/*     <div class="block_content">*/
/*         <!-- Products list -->*/
/*         <div id="product_list" class="products_block view-grid">*/
/*             <div class="row">*/
/*                 {% for prod in prods %}*/
/*                 <!-- Product item -->*/
/*                 <div class="p-item col-md-4 product_block ajax_block_product {% if loop.first %}first_item{% endif %}{% if loop.last %}last_item{% endif %} {% if loop.index%2==0 %} alternate_item{% else %} item{% endif %}  ">*/
/*                     <div class="list-products">*/
/*                         <div class="product-container clearfix">*/
/*                             <div class="center_block">*/
/*                                 <a href="{{ prod.Url }}" class="product_img_link" title="{{ prod.Nom }}"> <img src="/{% if prod.Image!= '' %}{{ prod.Image }} {% else %}Skins/[!Systeme::Skin!]/Img/image_def.jpg{% endif %}" alt="{{ prod.Nom }}" class="img-responsive" style="max-height:200px;margin: auto;" /> <span class="new">__NEW__</span> </a>*/
/*                                 {% if prod.Promo %}*/
/*                                 <span class="discount">__PROMO__</span>*/
/*                                 {% endif %}*/
/* */
/*                             </div>*/
/*                             <div class="right_block">*/
/*                                 <h3 class="s_title_block"><a href="{{ prod.Url }}" title="{{ prod.Nom }}">{{ prod.Nom }}</a></h3>*/
/* */
/*                                 <div class="price_container">*/
/*                                     </span>*/
/*                                     {% if prod.Promo %}*/
/*                                     <div style="display:block;color:#fff;font-size:13px;position:absolute;right:32px;text-decoration:line-through;top:0;" id="tarifNonPromo">*/
/*                                         {{ prod.getTarifHorsPromo() }}{{ CurrentDevise.Sigle }}*/
/*                                     </div>*/
/*                                     {% endif %}*/
/*                                     {% if prod.MultiTarif %}<span class="BlocProduitApartir">__A_PARTIR_DE__</span>{% endif %} <span class="price" style="display: inline;">{{ prod.Prix }}{{ CurrentDevise.Sigle }}</span>*/
/*                                     <br />*/
/*                                     {% if prod.StockReference %}*/
/*                                     <span class="availability">__AVAILABLE__</span>*/
/*                                     {% endif %}*/
/*                                 </div>*/
/*                                 <span class="online_only"></span>*/
/* */
/*                                 <a class="btn btn-success" href="{{ prod.Url }}" title="__ADD_TO_CART__">__ADD_TO_CART__</a>*/
/* */
/*                             </div>*/
/*                         </div>*/
/*                     </div>*/
/*                 </div>*/
/*                 <!-- /Product item -->*/
/*                 {% if loop.index%3==0 and loop.last==0 %}*/
/*             </div>*/
/*             <div class="row">*/
/*                 {% endif %}*/
/*                 {% endfor %}*/
/* */
/*             </div>*/
/*         </div>*/
/*         <!-- /Products list -->*/
/*     </div>*/
/* </div>*/
/* <!-- /MODULE Block specials -->*/
/* */
/* */
