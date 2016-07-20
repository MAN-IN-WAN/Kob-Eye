	

<footer>
	<div class="container nopadding-left nopadding-right">
		<div class="col-lg-3 col-sm-3 col-xs-12 nopadding-left">
			[OBJ Systeme|Menu|Me]
			[STORPROC [!Me::getBottomMenus()!]|M|0|2]
			<h4>[!M::Titre!]</h4>
			<ul class="dl">
				[STORPROC [!M::getSubMenus()!]|M2]
				<li><a href="/[!M::Url!]/[!M2::Url!]" data-filter=".[!M2::Url!]">[!M2::Titre!]</a></li>
				[/STORPROC]
			</ul>
			[/STORPROC]
		</div>
		<!-- /.col-lg-4 -->
		<div class="col-lg-3 col-sm-3  col-xs-12 col-marg">
			[STORPROC [!Me::getBottomMenus()!]/Affiche=1|M|2|2]
			<h4>[!M::Titre!]</h4>
			<ul class="dl">
				[STORPROC [!M::getSubMenus()!]/Affiche=1|M2]
				<li><a href="/[!M::Url!]/[!M2::Url!]" data-filter=".[!M2::Url!]">[!M2::Titre!]</a></li>
				[/STORPROC]
			</ul>
			[/STORPROC]
		</div><!-- /.col-lg-4 -->
             	<div class="col-lg-3 col-sm-3 col-xs-12">
                	<h4 class="closest">__CLOSEST_SHOP__</h4>
                  	<div id="myCarousel5" class="carousel slide">
                    		<div class="slide_footer">
                      			<div class="carousel-inner " id="closest_shop">
                        			<div class="active item">
                        				__LOADING__
						</div>
					<!-- Carousel nav -->
					</div>
					<a class="left carousel-control-adress" href="#myCarousel5" data-slide="prev"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-adresse-left.png" alt="prev"></a>
					<a class="right carousel-control-adress" href="#myCarousel5" data-slide="next"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-adresse-right.png" alt="next"></a>
    				</div>
			</div>    	
		</div>
	<!-- /.col-lg-4 -->
		<div class="col-lg-3 col-sm-3 col-xs-12 col-marg">
			<div class="socialLinks socialLinks-1">
        			<h4>__FOLLOW_US__</h4>
				<ul>
					<li class="pull-right" style="margin:3px;"><a href="https://www.facebook.com/F.oneInternational" target="_blank" class="fb" alt="Follow us on Facebook" title="Follow us on  Facebook">Follow us on  Facebook</a> </li>
					<li class="pull-right" style="margin:3px;"><a href="https://twitter.com/F_onekites" target="_blank" class="tw" alt="Follow us on Twitter" title="Follow us on " >Follow us on  Twitter</a> </li> 
					<li class="pull-right" style="margin:3px;"><a href="http://vimeo.com/fone" target="_blank" class="vimeo" alt="Follow us on  Vimeo" title="Follow us on  Vimeo">Follow us on  Vimeo</a></li>
					<li class="pull-right" style="margin:3px;"><a href="https://instagram.com/fonekites/" target="_blank" class="instagram" alt="Follow us on Instagram" title="Follow us on Instagram">Follow us on Instagram</a> </li>
				</ul>
			</div>    <!-- FOOTER -->
			<div class="newsletter">
				<h4>__NEWSLETTER__</h4>
				<form class="form-inline" role="form" id="newsletterform">
					<div class="form-group">
						<label class="sr-only" for="exampleInputEmail2">__EMAIL_ADDRESS__</label>
						<input type="email" class="form-control" id="exampleInputEmail2" placeholder="$EMAILINPUT$"  name="emailnewsletter" required>
					</div>
					<button type="submit" class="btn btn-input">OK</button>
					<input type="hidden" name="AddNewsletter" value="1"/>
                                </form>
                                <div class="modal fade" id="newsletter_modal">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title">__NEWSLETTER_SUBSCRIPTION__</h4>
                                      </div>
                                      <div class="modal-body">
                                        <p></p>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">__CLOSE__</button>
                                      </div>
                                    </div><!-- /.modal-content -->
                                  </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $('#newsletterform').submit(function (event){
					    event.preventDefault();
                                            $.ajax({
                                                dataType: "json",
                                                url: "/Newsletter/Contact/add.json?emailnewsletter="+$('#exampleInputEmail2').val()
                                            }).done(function (data){
						$('#newsletter_modal .modal-header h4').html('NEWSLETTER REGISTRATION');
                                                if (data.success){
                                                    $('#newsletter_modal .modal-header').removeClass('error');
                                                    $('#newsletter_modal .modal-header').addClass('success');
                                                    $('#newsletter_modal .btn.btn-default').removeClass('btn-danger');
                                                    $('#newsletter_modal .btn.btn-default').addClass('btn-success');
                                                }else{
                                                    $('#newsletter_modal .modal-header').removeClass('success');
                                                    $('#newsletter_modal .modal-header').addClass('error');
                                                    $('#newsletter_modal .btn.btn-default').removeClass('btn-success');
                                                    $('#newsletter_modal .btn.btn-default').addClass('btn-danger');
                                                }
                                                $('#newsletter_modal .modal-body p').html(data.message);
                                                $('#newsletter_modal').modal('show');
                                            });
                                        });
                                    });
                                </script>
			</div>
		</div><!-- /.col-lg-3 -->
	</div>
</footer>

