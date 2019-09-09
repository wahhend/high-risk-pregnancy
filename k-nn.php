<?php
    function eucDistance($x1, $x2) {
        return array_sum(
            // apply callback to array given, map function to each array element
            array_map(
                function($x, $y) {
                    return ($x - $y) ** 2;
                }, $x1, $x2
            )
        ) ** 0.5;
    }

    function getNeighbors($k, $test_data, $dataset, $labels){
        $distances = [];
        // count distance to each training data
        // assign to new array with label
        foreach ($dataset as $index => $train_data){
            $distances[] = array($labels[$index], eucDistance($test_data, $train_data));
        }
        // print_r($distances);
        // sort based on distance value
        // using spaceship operator
        usort($distances, function($a, $b) {
            return $a[1] <=> $b[1];
        });
        
        // take first k neighbor
        $neighbors = array_slice($distances, 0, $k);
        // printArray($neighbors);
        return $neighbors;
    }

    function distanceWeight($neighbors){
        $distances = array_column($neighbors, 1);
        $max = max($distances);
        $range = $max - min($distances);
        
        $weights = [];
        
        // calculate weight for each distance
        foreach ($distances as $distance){
            if ($distance == 0){
                $weights[] = 1;
            } else {
                $weights[] = ($max - $distance) / ($range + 0.1);
            }
        }

        // vote based on weights
        $votes = [];
        foreach ($weights as $index => $weight){
            if (array_key_exists($neighbors[$index][0][0], $votes)){
                $votes[$neighbors[$index][0][0]] += $weight;
            }
            else{
                $votes[$neighbors[$index][0][0]] = $weight;
            }
        }
        return $votes;
    }

    function inverseDistanceWeight($neighbors){
        $weight = [];

        foreach ($neighbors as $neighbor){
            $key = $neighbor[0][0];
            if (array_key_exists($key, $weight)){
                $weight[$key] += 1/($neighbor[1] + 0.1);
            }
            else{
                $weight[$key] = 1/($neighbor[1] + 0.1);
                // if ($neighbor[1] == 0){
                //     $weight[$key] = 1;
                // } else {
                //     $weight[$key] = 1/$neighbor[1];
                // }
            }
        }

        return $weight;
    }

    function inverseDistanceSquaredWeight($neighbors){
        $weight = [];

        foreach ($neighbors as $neighbor){
            if (array_key_exists($neighbor[0][0], $weight)){
                $weight[$neighbor[0][0]] += 1/($neighbor[1] + 1)**2;
            }
            else{
                if ($neighbor[1] == 0){
                    $weight[$neighbor[0][0]] = 1;
                } else {
                    $weight[$neighbor[0][0]] = 1/($neighbor[1] + 1)**2;
                }
            }
        }

        return $weight;
    }

    function gaussianWeight($neighbors){
        $weight = [];

        foreach ($neighbors as $neighbor){
            if (array_key_exists($neighbor[0][0], $weight)){
                $weight[$neighbor[0][0]] += 1/$neighbor[1]**2;
            }
            else{
                if ($neighbor[1] == 0){
                    $weight[$neighbor[0][0]] = 1;
                } else {
                    $weight[$neighbor[0][0]] = 1/$neighbor[1]**2;
                }
            }
        }

        return $weight;
    }

    function classify($k, $test_data, $dataset, $labels){
        $neighbors = getNeighbors($k, $test_data, $dataset, $labels);
        // printArray($neighbors);
        
        $result = distanceWeight($neighbors);
        // print_r($result);
        
        return array_search(max($result), $result);
    }

    function fit($k, $data_test, $label_test, $dataset, $labels){
        $total = count($data_test);
        $right = 0;
        foreach($data_test as $index => $data){
            // echo $index;
            $prediction = classify($k, $data, $dataset, $labels);
            $truth = $label_test[$index][0];
            if ($prediction == $truth){
                $right += 1;
            }
            // echo $prediction."|".$truth."<br>";
        }
        echo "Accuracy: ".$right."/".$total."=".$right/$total."<br>";
        
        return array($right, $total, $right/$total);
    }

    function printArray($array){
        foreach ($array as $value){
            if (gettype($value) == "array"){
                print_r($value);
            } else {
                echo $value;
            }
            
            echo "<br>";
        }
    }
?>