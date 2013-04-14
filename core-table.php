<?php


class TableFormatter {
	public function generate($persons, $commitCounts, $maxMonths) {

		$stats = $this->generateStats($persons, $commitCounts);

		$now = strtotime("first day of this month");
		/*****************************
		 * Generate Table Overview
		 *****************************/
		ob_start();
		echo '<?xml version="1.0" encoding="UTF-8"?>
		<html>
			<head>
				<title>Visualizing TYPO3 Core activity - statistics in tabled overview</title>
				<style type="text/css">
					* { margin: 0; padding: 0; font: 100.1% Helvetica, Arial, sans-serif; font-size: 11px; }
					table { margin: 20px; }
					a { color:grey; text-decoration:none; font-size: 13px; }
					a:hover { text-decoration:underline; }
					#description { font-size:13px; padding:10px 30px; }
					#footer { font-size:12px; padding:10px 30px; }
					td { width:160px; border-top:1px solid grey; border-left: 1px solid white; padding: 1px 3px;white-space:nowrap; }
				</style>
			</head>
			<body>
			<div id="description">
				Scoring is described in the related blog post
					<a href="http://blog.tolleiv.de/2012/01/visualizing-ty…-core-activity/">on visualization of the TYPO3 core activity.</a>
				The same numbers can also be found in the <a href="index.html">impact chart visualization</a>
			</div>
		';

		$cols = 6;
		for ($o = 0; $o < $maxMonths; $o += 6) {
			echo '<table cellspacing="0" cellpadding="0">';
			echo '<tr>';
			for ($i = $o; $i < $o + $cols; $i++) {
				if (!isset($stats[$i])) {
					break;
				}
				echo sprintf('<th>%s (<span title="no. of contributers">%d</span> / <span title="total score">%d</span>)</th>', $stats[$i]['month'], $stats[$i]['contributers'], $stats[$i]['total']);
			}
			echo '</tr>';

			for ($j = 0; $j < 20; $j++) {
				echo '<tr>';
				for ($i = $o; $i < $o + $cols; $i++) {
					$index = strftime('%Y.%m', strtotime($i ? '-' . $i . ' month' : 'now', $now));
					if (!isset($persons[$index])) {
						break;
					}
					$person = array_slice($persons[$index], $j, 1);
					if (!key($person)) {
						echo '<td style="border:none;"></td>';
					} else {
						echo sprintf('<td style="background-color:%s">%s (%s)</td>', $this->color(key($person)), key($person), current($person));
					}
				}
				echo '</tr>';
			}
			echo '</table>';
		}
		echo '
				</body>
			</html>
		';

		return ob_get_clean();
	}


	protected function generateStats($persons, $commitCounts) {
		$stats = array();
		$i = 0;
		foreach ($persons as $month => $data) {
			$stats[$i++] = array(
				'month' => $month,
				'commits' => $commitCounts[$month],
				'contributers' => count(array_keys($data)),
				'total' => array_sum($data),
				'top20' => array_sum(array_slice($data, 0, 20))
			);
		}
		return $stats;
	}

	/**
	 * Create a nice color for the table listings
	 *
	 * @param $name
	 * @return string
	 */
	protected function color($name) {
		$color = substr(md5($name), 6, 6);
		return sprintf('rgba(%s, %s, %s, 0.5)', hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
	}
}