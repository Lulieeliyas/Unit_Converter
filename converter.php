<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Unit Conversion Tool</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
        }
        h1 {
            text-align: center;
            color: black;
        }
        .converter {
            background-color: rgb(114, 56, 201);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 50px;
            color: white;
        }
        select, input, button {
            padding: 8px;
            margin: 5px 0;
            border: 1px solid white;
            border-radius: 4px;
            width: 100%;
        }
        button {
            background-color: rgb(10, 191, 247);
            color: white;
            cursor: pointer;
            border: none;
        }
        .result {
            margin-top: 10px;
            padding: 10px;
            background-color: #f6fdf9;
            border-radius: 4px;
            color: black;
        }
    </style>
</head>
<body>

<h1>PHP Unit Conversion Tool</h1>

<div class="converter">
    <form method="POST">
        <label><b>Category:</b></label>
        <select name="category" onchange="this.form.submit()">
            <?php
            $categories = ['length', 'weight', 'temperature', 'volume'];
            $selectedCategory = $_POST['category'] ?? 'length';
            foreach ($categories as $cat) {
                $selected = ($selectedCategory == $cat) ? 'selected' : '';
                echo "<option value=\"$cat\" $selected>" . ucfirst($cat) . "</option>";
            }
            ?>
        </select>

        <label><b>Value:</b></label>
        <input type="number" name="value" step="any" required value="<?= htmlspecialchars($_POST['value'] ?? '') ?>">

        <?php
        function getOptions($category) {
            $units = [
                'length' => ['cm'=>'Centimeters', 'm'=>'Meters', 'km'=>'Kilometers', 'in'=>'Inches', 'ft'=>'Feet', 'yd'=>'Yards', 'mi'=>'Miles'],
                'weight' => ['g'=>'Grams', 'kg'=>'Kilograms', 'oz'=>'Ounces', 'lb'=>'Pounds'],
                'temperature' => ['c'=>'Celsius (°C)', 'f'=>'Fahrenheit (°F)', 'k'=>'Kelvin (K)'],
                'volume' => ['ml'=>'Milliliters', 'l'=>'Liters']
            ];
            return $units[$category] ?? [];
        }

        $unitOptions = getOptions($selectedCategory);
        ?>

        <label><b>From:</b></label>
        <select name="from">
            <?php
            foreach ($unitOptions as $key => $label) {
                $selected = (($_POST['from'] ?? '') == $key) ? 'selected' : '';
                echo "<option value=\"$key\" $selected>$label</option>";
            }
            ?>
        </select>

        <label><b>To:</b></label>
        <select name="to">
            <?php
            foreach ($unitOptions as $key => $label) {
                $selected = (($_POST['to'] ?? '') == $key) ? 'selected' : '';
                echo "<option value=\"$key\" $selected>$label</option>";
            }
            ?>
        </select>

        <button type="submit" name="convert">Convert</button>
    </form>

    <div class="result">
        <?php
        if (isset($_POST['convert'])) {
            $value = floatval($_POST['value']);
            $from = $_POST['from'];
            $to = $_POST['to'];
            $cat = $_POST['category'];

            function getUnitLabel($unit) {
                $labels = [
                    'cm'=>'Centimeters', 'm'=>'Meters', 'km'=>'Kilometers', 'in'=>'Inches', 'ft'=>'Feet', 'yd'=>'Yards', 'mi'=>'Miles',
                    'g'=>'Grams', 'kg'=>'Kilograms', 'oz'=>'Ounces', 'lb'=>'Pounds',
                    'c'=>'Celsius', 'f'=>'Fahrenheit', 'k'=>'Kelvin',
                    'ml'=>'Milliliters', 'l'=>'Liters'
                ];
                return $labels[$unit] ?? $unit;
            }

            $result = null;
            if ($cat == 'length') {
                $toMeters = ['cm'=>0.01, 'm'=>1, 'km'=>1000, 'in'=>0.0254, 'ft'=>0.3048, 'yd'=>0.9144, 'mi'=>1609.344];
                $meters = $value * ($toMeters[$from] ?? 0);
                $fromMeters = ['cm'=>100, 'm'=>1, 'km'=>0.001, 'in'=>39.3701, 'ft'=>3.28084, 'yd'=>1.09361, 'mi'=>0.000621371];
                $result = $meters * ($fromMeters[$to] ?? 0);
            } elseif ($cat == 'weight') {
                $toGrams = ['g'=>1, 'kg'=>1000, 'oz'=>28.3495, 'lb'=>453.592];
                $grams = $value * ($toGrams[$from] ?? 0);
                $fromGrams = ['g'=>1, 'kg'=>0.001, 'oz'=>0.035274, 'lb'=>0.00220462];
                $result = $grams * ($fromGrams[$to] ?? 0);
            } elseif ($cat == 'temperature') {
                // Convert from source to Celsius
                if ($from == 'c') $c = $value;
                elseif ($from == 'f') $c = ($value - 32) * 5/9;
                elseif ($from == 'k') $c = $value - 273.15;
                else $c = null;

                // Convert from Celsius to target
                if ($to == 'c') $result = $c;
                elseif ($to == 'f') $result = $c * 9/5 + 32;
                elseif ($to == 'k') $result = $c + 273.15;
            } elseif ($cat == 'volume') {
                $toML = ['ml'=>1, 'l'=>1000];
                $ml = $value * ($toML[$from] ?? 0);
                $fromML = ['ml'=>1, 'l'=>0.001];
                $result = $ml * ($fromML[$to] ?? 0);
            }

            if ($result !== null) {
                echo "<strong>Result:</strong> $value " . getUnitLabel($from) . " = " . round($result, 4) . " " . getUnitLabel($to);
            } else {
                echo "<strong>Error:</strong> Conversion not supported.";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
