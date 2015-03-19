<!-- page header -->
<h1 id="page-header">Basic Elements</h1>

<div class="fluid-container">

	<!-- widget grid -->
	<section id="widget-grid" class="">

		<!-- row-fluid -->

		<div class="row-fluid">
			<article class="span12">
				<!-- new widget -->
				<div class="jarviswidget" id="widget-id-0">
					<!-- wrap div -->
					<header>
						<h2>Edition du contenu</h2>
					</header>
					<div>

						<div class="jarviswidget-editbox">
							<div>
								<label>Title:</label>
								<input type="text" />
							</div>
							<div>
								<label>Styles:</label>
								<span data-widget-setstyle="purple" class="purple-btn"></span>
								<span data-widget-setstyle="navyblue" class="navyblue-btn"></span>
								<span data-widget-setstyle="green" class="green-btn"></span>
								<span data-widget-setstyle="yellow" class="yellow-btn"></span>
								<span data-widget-setstyle="orange" class="orange-btn"></span>
								<span data-widget-setstyle="pink" class="pink-btn"></span>
								<span data-widget-setstyle="red" class="red-btn"></span>
								<span data-widget-setstyle="darkgrey" class="darkgrey-btn"></span>
								<span data-widget-setstyle="black" class="black-btn"></span>
							</div>
						</div>
						<div class="inner-spacer">
						        <!-- content goes here -->
							<form class="form-horizontal themed">
								<fieldset>
									<div class="control-group">
										<label class="control-label" for="input01">Text input</label>
										<div class="controls">
											<input type="text" class="span12"  id="input01" />
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="input02"><i class="icon-cog"></i>Has Icon</label>
										<div class="controls">
											<input type="text" class="span12"  id="input02" />
											<i class="icon-cog field-icon"></i>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="input03">Password input</label>
										<div class="controls">
											<input type="password" class="span12"  id="input03" />
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="input04">Text input with description</label>
										<div class="controls">
											<input type="text" class="span12"  id="input04" />
											<p class="help-block">
												In addition to freeform text, any HTML5 text-based input appears like so.
											</p>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="textarea">Textarea</label>
										<div class="controls">
											<textarea class="span12" id="textarea" rows="3"></textarea>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="focusedInput">Focused input</label>
										<div class="controls">
											<input class="span12 focused" id="focusedInput" type="text" value="This is focused" />
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="disabledInput">Disabled input</label>
										<div class="controls">
											<input class="span12 disabled" id="disabledInput" type="text" placeholder="Disabled input here" disabled="" />
										</div>
									</div>
									<div class="control-group info">
										<label class="control-label" for="inputInfo">Input with warning</label>
										<div class="controls">
											<input type="text" id="inputInfo" class="span12" />
											<span class="help-inline">Information input field</span>
										</div>
									</div>
									<div class="control-group success">
										<label class="control-label" for="inputSuccess">Input with success</label>
										<div class="controls">
											<input type="text" id="inputSuccess" class="span12" />
											<span class="help-inline">Woohoo!</span>
										</div>
									</div>
									<div class="control-group warning">
										<label class="control-label" for="inputWarning">Input with warning</label>
										<div class="controls">
											<input type="text" id="inputWarning" class="span12" />
											<span class="help-inline">Something may have gone wrong</span>
										</div>
									</div>
									<div class="control-group error">
										<label class="control-label" for="inputError">Input with error</label>
										<div class="controls">
											<input type="text" id="inputError" class="span12" />
											<span class="help-inline">Please correct the error</span>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label">Checkbox</label>
										<div class="controls">
											<label class="checkbox">
												<input type="checkbox" id="optionsCheckbox-5" value="option5">
												Unchecked 
											</label>
											<label class="checkbox">
												<input type="checkbox" id="optionsCheckbox-6" value="option6" checked="checked">
												Checked 
											</label>
											<label class="checkbox">
												<input type="checkbox" id="optionsCheckbox-7" value="option7" disabled="disabled">
												Disabled
											</label>
											<label class="checkbox">
												<input type="checkbox" id="optionsCheckbox-8" value="option8" checked="checked" disabled="disabled">
												Disabled Checked
											</label>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label">Radio Buttons</label>
										<div class="controls">
											<label class="radio">
											  <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
											  Option one
											</label>
											<label class="radio">
											  <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
											  Option two
											</label>
											<label class="radio">
											  <input type="radio" name="optionsRadios" id="optionsRadios3" value="option3" disabled="disabled">
											  Option three disabled
											</label>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label">Checkbox inline</label>
										<div class="controls">
											<label class="checkbox inline">
												<input type="checkbox" id="optionsCheckbox-1" value="option1">
												Unchecked 
											</label>
											<label class="checkbox inline">
												<input type="checkbox" id="optionsCheckbox-2" value="option2" checked="checked">
												Checked 
											</label>
											<label class="checkbox inline">
												<input type="checkbox" id="optionsCheckbox-3" value="option3" disabled="disabled">
												Disabled
											</label>
											<label class="checkbox inline">
												<input type="checkbox" id="optionsCheckbox-4" value="option4" checked="checked" disabled="disabled">
												Disabled Checked
											</label>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label">Radio Buttons inline</label>
										<div class="controls">
											<label class="radio inline">
											  <input type="radio" name="optionsRadios2" id="optionsRadios4" value="option1" checked>
											  Option one
											</label>
											<label class="radio inline">
											  <input type="radio" name="optionsRadios2" id="optionsRadios5" value="option2">
											  Option two
											</label>
											<label class="radio inline">
											  <input type="radio" name="optionsRadios2" id="optionsRadios6" value="option2" disabled="disabled">
											  Option three disabled
											</label>
										</div>
									</div>

									<div class="control-group">
										<label class="control-label">Date picker</label>
										<div class="controls">
											<div class="input-append date" id="datepicker-js" data-date="12-02-2012" data-date-format="dd-mm-yyyy">
												<input class="datepicker-input" size="16" type="text" value="12-02-2012" placeholder="Select a date" />
												<span class="add-on"><i class="cus-calendar-2"></i></span>
											</div>

											<p class="help-block">
												Default datepicker
											</p>
											<div class="space"></div>
											<div class="input-append date" id="datepicker-js-2" data-date="12-02-2012" data-date-format="dd-mm-yyyy">
												<input class="datepicker-input" size="16" type="text" value="12-02-2012" readonly />
												<span class="add-on"><i class="cus-calendar-2"></i></span>
											</div>
											<p class="help-block">Use as component</p>
										</div>
									</div>
									
									<div class="control-group">
										<label class="control-label">Color picker</label>
										<div class="controls">
											<div class="input-append color" data-color="ed1c24" data-color-format="hex" id="colorpicker-js">
												<input type="text" class="colorpicker-input" value="#ed1c24" />
												<span class="add-on"><i style="background-color: rgb(196, 26, 79);"></i></span>
											</div>
											<p class="help-block">
												HEX Colorpicker
											</p>
											<div class="space"></div>
											<div class="input-append color" data-color="rgb(255, 146, 180)" data-color-format="rgb" id="colorpicker-js-2">
												<input type="text" class="colorpicker-input" value="rgb(255, 146, 180)" readonly />
												<span class="add-on"><i style="background-color: rgb(196, 26, 79);"></i></span>
											</div>
											<p class="help-block">RBG Colorpicker</p>
											<div class="space"></div>
											<div class="input-append color" data-color="rgba(244,202,56,0.5)" data-color-format="rgba" id="colorpicker-js-3">
												<input type="text" class="colorpicker-input" value="rgba(244,202,56,0.5)" readonly />
												<span class="add-on"><i style="background-color: rgba(244,202,56,0.5);"></i></span>
											</div>
											<p class="help-block">RBGA Colorpicker</p>
										</div>
									</div>
									
									<div class="control-group" id="timepicker-demo">
										<label class="control-label">Time Picker</label>
										<div class="controls">
											<div class="input-append bootstrap-timepicker-component">
									            <input id="timepicker1" type="text" class="timepicker-input" value="05:30 PM" />
									            <span class="add-on"><i class="cus-clock"></i></span>
									        </div>
											<p class="help-block">
												Default
											</p>
											<div class="space"></div>
											<div class="input-append bootstrap-timepicker-component">
									            <input id="timepicker2" type="text" class="timepicker-input" value="17:33:45" />
									            <span class="add-on"><i class="cus-clock"></i></span>
									        </div>
											<p class="help-block">
												Display in model (24 hr style)
											</p>
										</div>
									</div>

									<div class="form-actions">
										<button type="reset" class="btn medium btn-danger">
											Cancel
										</button>
										<button type="submit" class="btn medium btn-primary">
											Save changes
										</button>
									</div>
								</fieldset>
							</form>
						</div>
						<!-- end content-->
					</div>
					<!-- end wrap div -->
				</div>
				<!-- end widget -->
			</article>
		</div>

		<!-- end row-fluid -->
	</section>
	<!-- end widget grid -->
</div>
