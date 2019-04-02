<?php
class Dice
{
    protected $figures = [1, 2, 3, 4, 5, 6];
    public function play($number = 2, $total = false)
    {
        // array_rand($a)
        $dices = [];
        $min = min($this->figures);
        if ($total === false || ($min * $number > $total)) {
            for ($i = 0; $i < $number; $i++) {
                $dices[] = $this->figures[array_rand($this->figures)];
            }
        } else {
            $max = max($this->figures);
            for ($i = 0; $i < $number; $i++) {
                $dices[] = $min;
            }
            while (array_sum($dices) < $total) {
                $key = array_rand($dices);
                if ($dices[$key] < $max) {
                    $dices[$key]++;
                }
            }
        }
        return $dices;
    }
}
