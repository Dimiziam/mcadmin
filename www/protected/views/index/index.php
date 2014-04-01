<?php
/* @var $this SiteController */

 $this->pageTitle=Yii::app()->name;


?>



	<script type="text/javascript">
		$(function(){
			$('select#genre').selectmenu({
					style:'DropDown',
					class:'mediaclub',
					maxHeight: 450,
					width: 180,
					menuWidth:180
					});
		})

	</script>
	<div class="grid_4">
		<div class="top_block"> <span>TOP 10</span> </div>
		<div class="block_header">Europe Albums</div>
		<div class="clear"></div>
		<div class="body_block">
			<div class="dropdown">
				<form action="POST">
					<fieldset>
						<select name="genre" id="genre">
							<option value="Slower">Slower</option>
							<option value="Slow" selected="selected">All genre</option>
							<option value="Medium">Medium</option>
							<option value="Fast">Fast</option>
							<option value="Faster">Faster</option>
						</select>
					</fieldset>
				</form>
			</div>
			<div class="block_content">
				<ol>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
				</ol>
			</div>
			<div class="more"><a href="#">MORE <img src="/images/more_arrow_right.png" /></a></div>
		</div>
	</div>
	<div class="grid_4">
		<div class="top_block"> <span>TOP 10</span> </div>
		<div class="block_header">World Albums</div>
		<div class="clear"></div>
		<div class="body_block">
			<div class="dropdown">
				<form action="POST">
					<fieldset>
						<select name="genre" id="genre">
							<option value="Slower">Slower</option>
							<option value="Slow" selected="selected">All genre</option>
							<option value="Medium">Medium</option>
							<option value="Fast">Fast</option>
							<option value="Faster">Faster</option>
						</select>
					</fieldset>
				</form>
			</div>
			<div class="block_content">
				<ol>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
					<li><span>PSY - </span>Gangnam Style</li>
					<li><span>Owl City And Carly Rae Jepsen</span> - Good Time</li>
				</ol>
			</div>
			<div class="more"><a href="#">MORE <img src="/images/more_arrow_right.png" /></a></div>
		</div>
	</div>
	<?php $this->widget('application.components.widgets.lists.WLatestAlbums'); ?>
	<div class="clear grid_12 spacer30"></div>
	<div class="grid_8">
		<div class="active_album">
			<div class="active_header">Now active albums
				<div class="header_pages">1 of 3</div>
			</div>
			<div class="active_content">
				<div class="suda"><a href="#suda">&nbsp;</a></div>
				<div class="content">
					<div class="grid_3">
						<div class="block_content_latest">
							<ul>
								<li>
									<div class="tumb"><img src="/images/tubm2.gif"  /></div>
									<div class="song_info"><span class="song_artist">PSY</span><br />
										Gangnam Style</div>
									<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
									<div class="clear"></div>
								<li>
									<div class="tumb"><img src="/images/tubm2.gif"  /></div>
									<div class="song_info"><span class="song_artist">Traveling Wilburys</span><br />
										The Secret Tapes Of Lucky Boo Boo BooBoo</div>
									<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
									<div class="clear"></div>
								</li>
								<li>
									<div class="tumb"><img src="/images/tubm2.gif"  /></div>
									<div class="song_info"><span class="song_artist">PSY</span><br />
										Gangnam Style</div>
									<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
									<div class="clear"></div>
								<li>
							</ul>
						</div>
					</div>
					<div class="grid_3">
						<div class="block_content_latest">
							<ul>
								<li>
									<div class="tumb"><img src="/images/tubm2.gif"  /></div>
									<div class="song_info"><span class="song_artist">PSY</span><br />
										Gangnam Style</div>
									<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
									<div class="clear"></div>
								</li>
								<li>
									<div class="tumb"><img src="/images/tubm2.gif"  /></div>
									<div class="song_info"><span class="song_artist">Traveling Wilburys</span><br />
										The Secret Tapes Of Lucky Boo Boo BooBoo</div>
									<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
									<div class="clear"></div>
								</li>
								<li>
									<div> <img src="/images/tumb.gif"  class="tumb"/><span class="song_info"><span class="song_artist">PSY</span><br />
										Gangnam Style</span></div>
									<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
									<div class="clear"></div>
								</li>
							</ul>
						</div>
					</div>
					<!-- /grid_3-->
					<div class="clear"></div>
					<div class="more"><a href="#">VIEW ALL <img src="/images/more_arrow_right.png" /></a></div>
				</div>
				<div class="tuda"><a href="#tuda">&nbsp;</a></div>
			</div>
		</div>
	</div>
	<div class="grid_4">
		<script type="text/javascript">
		$(function(){
			$('select#calendar').selectmenu({
					style:'DropDown',
					class:'mediaclub',
					maxHeight: 450,
					width: 180,
					menuWidth:180
					});
		})

	</script>
		<div class="history_header">History of additions</div>
		<div class="body_block_history">
			<div class="calendar_dropdown">
				<form action="POST">
					<fieldset>
						<select name="calendar" id="calendar">
							<option value="Slower">Slower</option>
							<option value="Slow" selected="selected">All genre</option>
							<option value="Medium">Medium</option>
							<option value="Fast">Fast</option>
							<option value="Faster">Faster</option>
						</select>
					</fieldset>
				</form>
			</div>
			<div class="clear"></div>
			<div class="calendar_view">
				<ul class="week_day">
					<li>Mo</li>
					<li>Tu</li>
					<li>We</li>
					<li>Th</li>
					<li>Fr</li>
					<li>Sa</li>
					<li>Su</li>
				</ul>
				<ul>
					<li>1</li>
					<li>2</li>
					<li>3</li>
					<li>4</li>
					<li>5</li>
					<li>6</li>
					<li>7</li>
				</ul>
			</div>
		</div>
	</div>
</div>  <!-- end container_12 -->

	<div class="clear"></div>





<div class="container_12">

	<!-- user top-->
	<div class="grid_12 spacer30"></div>
		<div class="clear"></div>
	 <!-- top7 from our users-->
	 <div id="from_users">
	<div class="grid_12">



		<div class="top_block"> <span>7 DAYS</span> </div>
								<div class="block_header">top from our users         <div class="header_pages">1 of 3</div></div>
								<div class="clear"></div>
								<div class="body_block">
									<div class="dropdown_from_users">
										<form action="POST">
											<fieldset>
												<select name="genre" id="genre">
													<option value="Slower">Slower</option>
													<option value="Slow" selected="selected">All genre</option>
													<option value="Medium">Medium</option>
													<option value="Fast">Fast</option>
													<option value="Faster">Faster</option>
												</select>
											</fieldset>
										</form>
									</div>
			</div>

		 <!--//grid_12 -->
			</div>      <!--//container_12 -->
<div class="clear"></div>

<div class="grid_12 album_view">
								<div class="suda"><a href="#suda">&nbsp;</a></div>



<!-- content top users-->
<div class="content">

				<div class=" spacer20 clear"></div>


				<div class="grid_2">
					<img src="images/user_top1.png" width="142" height="142" />
						<div class="artist">ZZ Top ZZ Top ZZ TopZZ Top ZZ Top ZZ Top</div>
						<div class="album">Greatest Hits</div>
			<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>



				</div>
				<div class="grid_2">
				<img src="images/user_top2.png" width="142" height="142" />
					<div class="artist">ZZ Top ZZ Top ZZ TopZZ Top ZZ Top ZZ Top</div>
						<div class="album">Bang Bang Boom Boom </div>
			<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>

				</div>
		<div class="grid_2 "><img src="images/user_top3.png" width="142" height="142" /></div>
				<div class="grid_2 "><img src="images/user_top4.png" width="142" height="142" /></div>
				<div class="grid_2 "><img src="images/user_top1.png" width="142" height="142" /></div>



				<div class="clear spacer20"></div>


						<div class="grid_2 ">
					<img src="images/user_top1.png" width="142" height="142" />
						<div class="artist">ZZ Top ZZ Top ZZ TopZZ Top ZZ Top ZZ Top</div>
						<div class="album">Greatest Hits</div>
			<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>



				</div>
				<div class="grid_2 ">
				<img src="images/user_top2.png" width="142" height="142" />
					<div class="artist">ZZ Top ZZ Top ZZ TopZZ Top ZZ Top ZZ Top</div>
						<div class="album">Bang Bang Boom Boom </div>
			<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>

				</div>


		<div class="grid_2"><img src="images/user_top3.png" width="142" height="142" />
					<div class="artist">ZZ Top ZZ Top ZZ TopZZ Top ZZ Top ZZ Top</div>
						<div class="album">Bang Bang Boom Boom </div>
			<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
				</div>
				<div class="grid_2 "><img src="images/user_top4.png" width="142" height="142" />
					<div class="artist">ZZ Top ZZ Top ZZ TopZZ Top ZZ Top ZZ Top</div>
						<div class="album">Bang Bang Boom Boom </div>
			<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
				</div>
				<div class="grid_2 "><img src="images/user_top1.png" width="142" height="142" />
					<div class="artist">ZZ Top ZZ Top ZZ TopZZ Top ZZ Top ZZ Top</div>
						<div class="album">Bang Bang Boom Boom </div>
			<div class="song_style"><a href="#">Rock</a> / <a href="#">Metal</a> / <a href="#">Doom</a> </div>
				</div>


<!-- //top7 from our users-->

</div><!-- //content-->

								<div class="tuda"><a href="#tuda">&nbsp;</a></div>
</div> <!--// grid_12 album_view -->


<div class="clear spacer80"></div>




		</div>

</div><!--/ id=from_users-->



