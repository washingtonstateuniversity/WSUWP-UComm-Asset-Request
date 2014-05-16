<?php
/*
Plugin Name: WSU graph
Plugin URI: http://ucomm.wsu.edu/
Description: Allows users to register for assets.
Author: washingtonstateuniversity, jeremyfelt
Author URI: http://web.wsu.edu/
Version: 0.1.3
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class graph {

	/**
	 * Setup the hooks.
	 */
	public function __construct() {

		add_shortcode( 'graph',    array( $this, 'graph_display' ) );
	}

	/**
	 * Handle the display of the svg_ shortcode.
	 *
	 * @return string HTML output
	 */
	public function graph_display() {
		// Build the output to return for use by the shortcode.
		ob_start();
		?>
		<div id="concussion-game-plan">
<section class="not-just-football" data-track-visitor-engagement="not-just-football">
<div id="stats" class="stats">
<div id="football-stat" class="stat">

<figure class="total"><img src="http://images.apple.com/your-verse/concussion-game-plan/images/football_stats_total.png" alt="" width="145" height="62" /></figure><figure class="icon"><img src="http://images.apple.com/your-verse/concussion-game-plan/images/football_stats_icon.png" alt="" width="88" height="88" /></figure>
<h2><img src="http://images.apple.com/your-verse/concussion-game-plan/images/football_stats_title.png" alt="Football - 153,000" width="140" height="16" /></h2>
</div>
<div id="soccer-stat" class="stat">

<figure class="total"><img src="http://images.apple.com/your-verse/concussion-game-plan/images/soccer_stats_total.png" alt="" width="145" height="62" /></figure><figure class="icon"><img src="http://images.apple.com/your-verse/concussion-game-plan/images/soccer_stats_icon.png" alt="" width="88" height="88" /></figure>
<h2><img src="http://images.apple.com/your-verse/concussion-game-plan/images/soccer_stats_title.png" alt="Soccer - 100,000" width="140" height="16" /></h2>
</div>
<div id="basketball-stat" class="stat lacrosse">

<figure class="total"><img src="http://images.apple.com/your-verse/concussion-game-plan/images/basketball_stats_total.png" alt="" width="145" height="62" /></figure><figure class="icon"><img src="http://images.apple.com/your-verse/concussion-game-plan/images/basketball_stats_icon.png" alt="" width="88" height="88" /></figure>
<h2><img src="http://images.apple.com/your-verse/concussion-game-plan/images/basketball_stats_title.png" alt="Basketball - 29,000" width="140" height="16" /></h2>
</div>
<div id="wrestling-stat" class="stat soccer">

<figure class="total"><img src="http://images.apple.com/your-verse/concussion-game-plan/images/wrestling_stats_total.png" alt="" width="145" height="62" /></figure><figure class="icon"><img src="http://images.apple.com/your-verse/concussion-game-plan/images/wrestling_stats_icon.png" alt="" width="88" height="88" /></figure>
<h2><img src="http://images.apple.com/your-verse/concussion-game-plan/images/wrestling_stats_title.png" alt="Wrestling - 12,000" width="140" height="16" /></h2>
</div>
</div>
<!--/stats-->
</section>

		<?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}
new graph();
