<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <!-- Form fields go here -->
         <input type="string" name="num1" placeholder="Enter first number">
         <select name="operator">
             <option value="+">+</option>
             <option value="-">-</option>
             <option value="*">×</option>
             <option value="/">÷</option>
        </select>
        <input type="number" name="num2" placeholder="Enter second number">

        <input type="submit" value="Calculate">
            </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $num1=filter_input(INPUT_POST, "num1", FILTER_SANITIZE_NUMBER_FLOAT);
        $num2=filter_input(INPUT_POST, "num2", FILTER_SANITIZE_NUMBER_FLOAT);
        $operation=htmlspecialchars($_POST["operator"]);
        $result=0;

        if(empty($num1) || empty($num2))
            {
                echo "<p> please fill all the fields</p>";
            }
        else if (!is_numeric($num1) || !is_numeric($num2)) {
            echo "<p> please enter valid numbers</p>";
        }
        else {
        switch ($operation) {
            case "+":
                $result=$num1+$num2;
                break;
            case "-":
                $result=$num1-$num2;
                break;
            case "*":
                $result=$num1*$num2;
                break;
            case "/":
                if ($num2 != 0) {
                    $result=$num1/$num2;
                } else {
                    echo "Cannot divide by zero.";
                    exit();
                }
                break;
            default:
                echo "Invalid operation.";
                exit();
        }
        }
        echo "Result: ", $result;
    }
    ?>
</body>
</html>