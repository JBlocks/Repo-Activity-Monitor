<?php


class TableFormatter {
	public function generate($persons, $commitCounts, $maxMonths) {

		$tableHtml = '';
		$stats = $this->generateStats($persons, $commitCounts);
		$now = strtotime("first day of this month");
		$cols = 6;

		for ($o = 0; $o < $maxMonths; $o += 6) {
			$tableHtml .= '<table class="table">';
			$tableHtml .= '<tr>';
			for ($i = $o; $i < $o + $cols; $i++) {
				if (!isset($stats[$i])) {
					break;
				}
				$tableHtml .= sprintf('<th>%s (<span title="no. of contributers">%d</span> / <span title="total score">%d</span>)</th>', $stats[$i]['month'], $stats[$i]['contributers'], $stats[$i]['total']);
			}
			$tableHtml .= '</tr>';

			for ($j = 0; $j < 20; $j++) {
				$tableHtml .= '<tr>';
				for ($i = $o; $i < $o + $cols; $i++) {
					$index = strftime('%Y.%m', strtotime($i ? '-' . $i . ' month' : 'now', $now));
					if (!isset($persons[$index])) {
						break;
					}
					$person = array_slice($persons[$index], $j, 1);
					if (!key($person)) {
						$tableHtml .= '<td style="border:none;"></td>';
					} else {
						$tableHtml .= sprintf('<td style="background-color:%s">%s (%s)</td>', $this->color(key($person)), key($person), current($person));
					}
				}
				$tableHtml .= '</tr>';
			}
			$tableHtml .= '</table>';
		}
		
		return $tableHtml;
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
