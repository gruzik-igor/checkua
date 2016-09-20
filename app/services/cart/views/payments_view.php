<div class="row">
	<div class="col-md-6 md-margin-bottom-50">
		<h2 class="title-type">Оберіть платіжний механізм</h2>
		<!-- Accordion -->
		<div class="accordion-v2">
			<div class="panel-group" id="accordion">
				<?php 
				$cart = $this->db->getAllDataById('s_cart', $_SESSION['cart']->id);
				if($cart)
				{
					$cart->return_url = $_SESSION['alias']->alias.'/order/'.$cart->id;
					$cart->wl_alias = $_SESSION['alias']->id;
					$cooperation_where['alias1'] = $_SESSION['alias']->id;
					$cooperation_where['type'] = 'payment';
					$cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where);
			        if($cooperation)
			        {
			            foreach ($cooperation as $storage) {
			                $this->load->function_in_alias($storage->alias2, '__get_Payment', $cart);
			            }
			        }
				}
				?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
								<i class="fa fa-paypal"></i>
								Pay with PayPal
							</a>
						</h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse">
						<div class="content margin-left-10">
							<a href="#"><img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_150x38.png" alt="PayPal"></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Accordion -->
	</div>

	<div class="col-md-6">
		<h2 class="title-type">Frequently asked questions</h2>
		<!-- Accordion -->
		<div class="accordion-v2 plus-toggle">
			<div class="panel-group" id="accordion-v2">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion-v2" href="#collapseOne-v2">
								What payments methods can I use?
							</a>
						</h4>
					</div>
					<div id="collapseOne-v2" class="panel-collapse collapse in">
						<div class="panel-body">
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit, felis vel tincidunt sodales, urna metus rutrum leo, sit amet finibus velit ante nec lacus. Cras erat nunc, pulvinar nec leo at, rhoncus elementum orci. Nullam ut sapien ultricies, gravida ante ut, ultrices nunc.
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" class="collapsed" data-parent="#accordion-v2" href="#collapseTwo-v2">
								Can I use gift card to pay for my purchase?
							</a>
						</h4>
					</div>
					<div id="collapseTwo-v2" class="panel-collapse collapse">
						<div class="panel-body">
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit, felis vel tincidunt sodales, urna metus rutrum leo, sit amet finibus velit ante nec lacus. Cras erat nunc, pulvinar nec leo at, rhoncus elementum orci. Nullam ut sapien ultricies, gravida ante ut, ultrices nunc.
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" class="collapsed" data-parent="#accordion-v2" href="#collapseThree-v2">
								Will I be charged when I place my order?
							</a>
						</h4>
					</div>
					<div id="collapseThree-v2" class="panel-collapse collapse">
						<div class="panel-body">
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit, felis vel tincidunt sodales, urna metus rutrum leo, sit amet finibus velit ante nec lacus. Cras erat nunc, pulvinar nec leo at, rhoncus elementum orci. Nullam ut sapien ultricies, gravida ante ut, ultrices nunc.
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" class="collapsed" data-parent="#accordion-v2" href="#collapseFour-v2">
								How long will it take to get my order?
							</a>
						</h4>
					</div>
					<div id="collapseFour-v2" class="panel-collapse collapse">
						<div class="panel-body">
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit, felis vel tincidunt sodales, urna metus rutrum leo, sit amet finibus velit ante nec lacus. Cras erat nunc, pulvinar nec leo at, rhoncus elementum orci. Nullam ut sapien ultricies, gravida ante ut, ultrices nunc.
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Accordion -->
	</div>
</div>