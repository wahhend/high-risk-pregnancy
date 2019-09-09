<?php
    include 'k-nn.php';

    function loadDataset($data_filename, $label_filename){
        $data = fopen($data_filename, 'r');
        $label = fopen($label_filename, 'r');
        
        // append every line of csv to 2 dimensional array
        while (($data_row = fgetcsv($data)) && ($label_row = fgetcsv($label))) {
            $dataset[] = $data_row;
            $labels[] = $label_row;
        }

        fclose($data);
        fclose($label);
        
        // remove header (column name)
        array_shift($dataset);

        return array($dataset, $labels);
    }

    function seq_k_fold($k, $dataset, $labels){
        // print_dataset($dataset, $labels);
        $kfornn = [2, 3, 5, 6, 7, 8, 9, 10];
        // $kfornn = [12, 15, 20, 25, 30];
        $fold = floor(count($dataset)/$k);
        echo "<h2>Data each fold: ".$fold."</h2>";
        $result = [];
        for($i = 0; $i < $k; $i++){
            echo "<h2>".$i."</h2>";
            echo "<h3>".$i*$fold." ".(($i+1)*$fold-1)."</h3>";

            $data_train = $dataset;
            $label_train = $labels;

            $data_test = array_splice($data_train, $i*$fold, $fold);
            $label_test = array_splice($label_train, $i*$fold, $fold);
            
            // printArray($data_test);
            // printArray($data_train);
            echo "<h2>Num of data:".count($dataset)."</h2>";
            echo "<h2>Num of test data:".count($data_test)."</h2>";            
            echo "<h2>Num of training data:".count($data_train)."</h2>";
            $k_result = [];
            foreach($kfornn as $knn){
                echo "<h2>k = ".$knn."</h2>";
                $k_result[] = fit($knn, $data_test, $label_test, $data_train, $label_train);
            }
            $result[] = $k_result;
        }
        
        return $result;
    }

    function ran_k_fold($k, $dataset, $labels){

    }

    function print_dataset($dataset, $labels){
        foreach($dataset as $index => $data){
            print_r($data);
            print_r($labels[$index]);
            echo "<br>";
        }
    }

    $data_filename = 'data/data_normalized.csv';
    $label_filename = 'data/label.csv';

    list($dataset, $labels) = loadDataset($data_filename, $label_filename);

    $result = seq_k_fold(10, $dataset, $labels);

    printArray($result);
    $avgAccuracy = [];
    for ($i = 0; $i<count($result[$i]); $i++){
        $avgK = 0;
        for ($j = 0; $j<count($result); $j++){
            $avgK += $result[$j][$i][2];
        }
        $avgK /= 10;
        $avgAccuracy[] = $avgK;
    }
    printArray($avgAccuracy);
?>