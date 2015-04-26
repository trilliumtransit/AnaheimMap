<?php

?>

<div id="planner">
	<h2 class="Dense-Regular">Find a Bus Route</h2>
	<div id="inner-planner">
	<h3 id="from-header">From</h3>
	<div id="from-items-container" class="items-container open">
		<ul>
			<li>
				<div class="tab-holder"><a href="javascript:void(0)">Attractions</a></div>
				<div class="place-pannel">
					<div class="next-button"> 
						<i class="upper-arrow"></i>
						<div class="more">More</div>
						<i class="lower-arrow"></i>
					</div><!-- end #next-button -->
					<div class="panel-tab panel-tab-0 showing"> 
					
					<?php makePlacePanel('attractions', -1, -1); ?>
					
						
					</div><!-- end .panel-tab -->
				</div><!-- end place-pannel -->
			</li>
			<li class="active"> 
				<div class="tab-holder"><a href="javascript:void(0)">Hotels</a></div>
				<div class="place-pannel" rel="0">
				<div class="next-button"> 
						<i class="upper-arrow"></i>
						<div class="more">More</div>
						<i class="lower-arrow"></i>
					</div><!-- end #next-button -->
					<div class="panel-tab panel-tab-0 showing"> 
					
					<?php makePlacePanel('hotel', -1, -1); ?>
					
						
					</div><!-- end .panel-tab -->
					
				</div><!-- end place-pannel -->
			</li>
			<li>
				<div class="tab-holder"><a href="javascript:void(0)">Restaurants</a></div>
				<div class="place-pannel">
					<div class="next-button"> 
						<i class="upper-arrow"></i>
						<div class="more">More</div>
						<i class="lower-arrow"></i>
					</div><!-- end #next-button -->
					<div class="panel-tab panel-tab-0"> 
							<?php makePlacePanel('restaurant', -1, -1); ?>
					</div><!-- end .panel-tab -->
				</div><!-- end place-pannel -->
			</li>
			</ul>
		
	</div><!-- end #from-items-container -->
	<form>
	<input class="loc-input" type="text" name="from-loc" placeholder= "Or enter a starting address">
	
	<h3 id="to-header">TO</h3>
	<div id="to-items-container" class="items-container" class="">
		<ul>
			<li>
				<div class="tab-holder"><a href="javascript:void(0)">Attractions</a></div>
				<div class="place-pannel">
					<div class="next-button"> 
						<i class="upper-arrow"></i>
						<div class="more">More</div>
						<i class="lower-arrow"></i>
					</div><!-- end #next-button -->
					<div class="panel-tab panel-tab-0 showing"> 
					
					<?php makePlacePanel('attractions', -1, -1); ?>
					
						
					</div><!-- end .panel-tab -->
				</div><!-- end place-pannel -->
			</li>
			<li class="active"> 
				<div class="tab-holder"><a href="javascript:void(0)">Hotels</a></div>
				<div class="place-pannel" rel="0">
				<div class="next-button"> 
						<i class="upper-arrow"></i>
						<div class="more">More</div>
						<i class="lower-arrow"></i>
					</div><!-- end #next-button -->
					<div class="panel-tab panel-tab-0 showing"> 
					
					<?php makePlacePanel('hotel', -1, -1); ?>
					
						
					</div><!-- end .panel-tab -->
					
				</div><!-- end place-pannel -->
			</li>
			<li>
				<div class="tab-holder"><a href="javascript:void(0)">Restaurants</a></div>
				<div class="place-pannel">
					<div class="next-button"> 
						<i class="upper-arrow"></i>
						<div class="more">More</div>
						<i class="lower-arrow"></i>
					</div><!-- end #next-button -->
					<div class="panel-tab panel-tab-0"> 
							<?php makePlacePanel('restaurant', -1, -1); ?>
					</div><!-- end .panel-tab -->
				</div><!-- end place-pannel -->
			</li>
			</ul>
		
	</div><!-- end #to-items-container -->
	<input type="text" name="from-loc" placeholder= "Or enter your destination's address">
	<input type="submit" value="Show Travel Plan" >
	</form>
	<br style="clear:both;">
	</div><!-- end #inner-planner -->
</div>