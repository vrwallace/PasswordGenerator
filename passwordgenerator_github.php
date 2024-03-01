<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use GuzzleHttp\Client;

$difficultyLevels = [
    'normal' => [],
    'harder' => [
        'a' => '@',
        'A' => '@',
        'c' => '(',
        'C' => '(',
        'b' => '8',
        'B' => '8',
        'e' => '3',
        'E' => '3',
        'l' => '1',
        'L' => '1',
        's' => '5',
        'S' => '5',
        't' => '7',
        'T' => '7',
        'z' => '2',
        'Z' => '2',
        'i' => '!',
        'I' => '!',

        // add more substitutions as needed
    ],
];


function passwordCrackTimeEstimate($password) {
    // Number of guesses per second
    $guessesPerSecond = 10**9;

    // Password length
    $length = strlen($password);



    // Estimate password complexity
    $complexity = 0;
    if (preg_match('/[a-z]/', $password)) $complexity += 26; // Lowercase letters
    if (preg_match('/[A-Z]/', $password)) $complexity += 26; // Uppercase letters
    if (preg_match('/[0-9]/', $password)) $complexity += 10; // Digits
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $complexity += 32; // Special characters (basic ASCII)

    // Calculate the total number of possible passwords
    $totalPasswords = bcpow($complexity, $length);

    // Estimate the time to crack in seconds (assuming every possible password is tried)
    $timeToCrackSeconds = bcdiv($totalPasswords, $guessesPerSecond);

    // Convert seconds to years for readability
    $timeToCrackYears = bcdiv($timeToCrackSeconds, 60*60*24*365);

    // Convert years to scientific notation
    $timeToCrackYearsScientific = sprintf('%e', $timeToCrackYears);

    return $timeToCrackYearsScientific;
}


function checkCategories() {
    $client = new Client([
        'base_uri' => 'https://api.datamuse.com/',
    ]);

    $categories = [

        // your categories here
    ];

    $emptyCategories = [];

    foreach($categories as $category) {
        $response = $client->request('GET', 'words?rel_trg=' . $category);
        $data = json_decode($response->getBody());

        if(empty($data)) {
            $emptyCategories[] = $category;
        }
    }

    return $emptyCategories;
}








function getWord() {
    $client = new Client([
        'base_uri' => 'https://api.datamuse.com/',
    ]);

    $categories = [

        'fruit',
        'color',
        'animal',
        'city',
        'country',
        'vegetable',
        'car',
        'flower',
        'book',
        'music',
        'movie',
        'language',
        'instrument',
        'furniture',
        'electronic',
        'sport',
        'author',
        'actor',
        'director',
        'painter',
        'philosopher',
        'scientist',
        'island',
        'planet',
        'constellation',
        'software',
        'website',
        'podcast',
        'poet',
        'beverage',
        'cocktail',
        'dessert',
        'candy',
        'celebrity',
        'sculptor',
        'architect',
        'museum',
        'painting',
        'novel',
        'poem',
        'song',
        'album',
        'band',
        'musician',
        'play',
        'playwright',
        'magazine',
        'newspaper',
        'website',
        'app',
        'company',
        'CEO',
        'entrepreneur',
        'technology',
        'stock',
        'currency',
        'capital',
        'continent',
        'ocean',
        'sea',
        'river',
        'mountain',
        'desert',
        'forest',
        'biome',
        'element',
        'compound',
        'chemical',
        'mineral',
        'rock',
        'gemstone',
        'tree',
        'plant',
        'herb',
        'spice',
        'disease',
        'medication',
        'vitamin',
        'protein',
        'athlete',
        'team',
        'stadium',
        'arena'

        // your categories here
    ];

    $data = [];
    $remainingCategories = $categories;  // keep a copy of categories for removing tried ones

    while (empty($data) && !empty($remainingCategories)) {
        $categoryIndex = array_rand($remainingCategories);
        $category = $remainingCategories[$categoryIndex];
        $response = $client->request('GET', 'words?rel_trg=' . $category);
        $data = json_decode($response->getBody());

        // Remove the tried category from the remaining ones
        unset($remainingCategories[$categoryIndex]);
    }

    return !empty($data) ? $data[array_rand($data)]->word : "";
}


function getPassphrase($maxLength, &$generatedPassphrases, $difficulty) {
    global $difficultyLevels;  // bring global variable into function scope
    $substitutions = $difficultyLevels[$difficulty];

    $special_chars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')'];
    $special_char = $special_chars[array_rand($special_chars)];
    $special_char_separator = $special_chars[array_rand($special_chars)];

    do {
        if (rand(0, 1) == 0) {
            $word1 = ucfirst(getWord());
            $word2 = lcfirst(getWord());
        } else {
            $word1 = lcfirst(getWord());
            $word2 = ucfirst(getWord());
        }
        $num = rand(10, 99);

        $passphrase = $word1 . $special_char_separator . $word2 . $special_char_separator . $num . $special_char;

        // Apply substitutions to $passphrase
        $newPassphrase = '';
        foreach (str_split($passphrase) as $character) {
            if (isset($substitutions[$character])) {
                $newPassphrase .= $substitutions[$character];
            } else {
                $newPassphrase .= $character;
            }
        }
        $passphrase = $newPassphrase;
//echo $passphrase."<br>";
//echo strlen($passphrase)."<br>";
//echo  $maxLength."<br>";
 } while ((strlen($passphrase) > (int)$maxLength) || (isset($generatedPassphrases[$passphrase])));
  // } while (strlen($passphrase) > (int)$maxLength );

    $generatedPassphrases[$passphrase] = true;

    return $passphrase;
}



$header = "<meta name=\"description\" content=\"Efficient tool for creating secure, unique passwords. Perfect for account creation and password updates, balancing complexity and memorability.\">
<meta name=\"keywords\" content=\"Password Generator, Secure Password, Unique Password, Account Security, Password Updates\">
<link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"/apple-touch-icon.png\"/>
<link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"/favicon-32x32.png\"/>
<link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"/favicon-16x16.png\"/>
<link rel=\"canonical\" href=\"/network/passwordgenerator.php\"/>

<style>
    .copy-btn {
        cursor: pointer;
        border: 1px solid #000;
        padding: 5px;
        margin-left: 10px;
        display: inline-block;
        text-align: center;
       // border-radius: 25px;  // Make button rounded
       // background-color: #ffc680;  // Same as Bootstrap's btn-primary
        color: #000;  // Make text black
    }
    .copy-btn:hover {
        color: #fff;  // Change text color to white on hover
        background-color: #0069d9;  // Darken the button a bit on hover
    }
    </style>

";

echo "<!DOCTYPE html>
<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en\" xml:lang=\"en\">


<head>
<meta charset=\"UTF-8\"/>
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />

<link rel=\"stylesheet\" href=\"/css/vwimprove.css\"/>
<link rel=\"stylesheet\" href=\"/css/pagestyling.css\"/>
<link rel=\"stylesheet\" href=\"/css/phonelookup.css\"/>
<meta property=\"og:image\" content=\"https://vonwallace.com/mstile-150x150.png\" />

<style>.hr1 {height: 0;width:40%;margin: auto;border-bottom: 1px solid white;}</style>


 <link href=\"/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\"  crossorigin=\"anonymous\">
<script src=\"/bootstrap/js/bootstrap.bundle.min.js\"  crossorigin=\"anonymous\"></script>
    <style>body {background-color: #000;
    color:#fff;}</style>

<title>Secure Password Generator | Balance of Complexity and Memorability</title>", $header,
" </head>
<body >";

$filename = "../menu/menu5.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);
echo $contents;

echo "<div class=\"wrapper\"> 
<div class=\"hr1\"></div>
<div class=\"htitle\">PASSWORD GENERATOR</div>
<div class=\"hr1\"></div>
<div class=\"htime\">", date("l jS \of F Y h:i:s A"), "<br/></div><br/><br/><hr/>";

$passphrases = [];
$generatedPassphrases = [];
$minLength = 16;
//$minimumAcceptableLength = 12; // Adjust the minimum acceptable length as needed

$maxLength =16;

//**** if you set the max length shorter than 16 it will take a long time to come up with passowords that are short enough. So do not do this.

    //max($minimumAcceptableLength, $minLength); // Set maxLength to the higher value between minimumAcceptableLength and minLength by default

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['difficulty'])) {
    $difficulty = $_POST['difficulty'];
    $return = "<script type=\"text/javascript\">document.getElementById(\"difficulty\").value =\"" . $_POST['difficulty']. "\"</script>";
    echo $return;

} else {
    $difficulty = 'normal';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maxLength'])) {
    $maxLength = max((int)$_POST['maxLength'], $minLength);
}
//echo $maxLength;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    for ($i = 0; $i < 5; $i++) {
        //$passphrases[] = getPassphrase($maxLength, $generatedPassphrases);
        $passphrases[] = getPassphrase($maxLength, $generatedPassphrases, $difficulty);
    }
} else {
    //$passphrases[] = getPassphrase($maxLength, $generatedPassphrases);
    $passphrases[] = getPassphrase($maxLength, $generatedPassphrases, $difficulty);
}

?>


<form method="post">
    <label for="difficulty">Difficulty to remember:</label>
    <select id="difficulty" name="difficulty">
        <option value="normal">Normal</option>
        <option value="harder">Harder</option>
    </select>
    <label for="maxLength">Max password length:</label>
    <input type="number" id="maxLength" name="maxLength" value="<?= htmlspecialchars($maxLength); ?>" min="<?= $minLength ?>"><input type="submit" value="Generate"> (Anticipate Delay)
</form>
<?php
if (!empty($passphrases)) {

/*    $emptyCategories = checkCategories();
    if (!empty($emptyCategories)) {
        echo "The following categories are empty: " . implode(", ", $emptyCategories)."<br />";
    }*/





    echo'<br /><div class="table_style1">';
    echo'<table><tr><th colspan="1">PASSWORDS</th><th>LENGTH</th><th></th><th>YEARS TO CRACK (ESTIMATE)</th></tr>';


    foreach ($passphrases as $passphrase) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($passphrase, ENT_QUOTES, 'UTF-8') . '</td>';
       echo '<td>'.strlen($passphrase). '</td>';
        echo '<td><button class="copy-btn btn btn-primary text-dark" data-password="' .$passphrase. '">Copy to clipboard</button></td>';
        echo '<td>'.passwordCrackTimeEstimate($passphrase).'</td>';
        echo '</tr>';
    }
    echo '</table></div>';

    echo '<br /><p>🌟 Note: This password generator creates secure, unique passwords ideal for new accounts or updates, blending complexity with ease of use. For better security, pair it with methods like two-factor authentication (2FA). Words are sourced randomly from an online API, sometimes leading to non-politically correct terms.</p>';

}
echo "<hr/><div class=\"center\"><b><i><a href=\"https://www.biblegateway.com/passage/?search=1+John+4%3A7-21&amp;version=NIV\" target=\"_blank\">God Is Love - 1 John 4:7-21</a></i></b><br /><br /></div></div>\n";
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.copy-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const password = button.dataset.password;
                navigator.clipboard.writeText(password)
                    .then(function() {
                        // Alert the user
                        alert('Password copied to clipboard: ' + password);
                    })
                    .catch(function(error) {
                        // Handle errors
                        console.error('Failed to copy password to clipboard: ', error);
                    });
            });
        });
    });
</script>

</body>
</html>

