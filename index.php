<?php
require 'vendor/autoload.php';
require 'src/Zookeeper.php';

use Carbon\Carbon;
use Jawira\EmojiCatalog\Emoji;
use Zookeeper\Zookeeper;

class Animal {
    public string $name;
    public int $happiness;
    public string $foodType;
    public int $foodReserves;
    public Carbon $lastPlayTime;
    public Carbon $lastWorkTime;
    public Carbon $lastFeedTime;

    public function __construct(string $name, int $happiness, string $foodType) {
        $this->name = $name;
        $this->happiness = $happiness;
        $this->foodType = $foodType;
        $this->foodReserves = 100;
        $this->lastPlayTime = Carbon::now();
        $this->lastWorkTime = Carbon::now();
        $this->lastFeedTime = Carbon::now();
    }

    public function play(int $seconds): void {
        $this->happiness += 2 * $seconds;
        $this->foodReserves -= 1 * $seconds;
        $this->lastPlayTime = Carbon::now();
        echo "You played with $this->name for $seconds seconds and now their happiness is 
        {$this->happiness} - food reserves $this->foodReserves. " . Emoji::SMILING_FACE_WITH_HEARTS . "\n";
    }

    public function work(): void {
        $this->happiness -= 10;
        $this->foodReserves += 10;
        $this->lastWorkTime = Carbon::now();
        echo "$this->name, has worked very hard worked and now their happiness is 
        {$this->happiness} - food reserves $this->foodReserves. " . Emoji::SLIGHTLY_SMILING_FACE . "\n";
    }

    public function feed(string $food): void {
        if ($food == $this->foodType) {
            $this->happiness += 5;
            $this->foodReserves += 10;
            $this->lastFeedTime = Carbon::now();
            echo "You fed {$this->name} the correct food and now their happiness is 
            $this->happiness - food reserves $this->foodReserves. " . Emoji::SMILING_FACE_WITH_HEARTS . "\n";
        } else {
            $this->happiness -= 5;
            $this->foodReserves -= 20;
            $this->lastFeedTime = Carbon::now();
            echo "You fed $this->name the wrong food and now their happiness is 
            {$this->happiness} - food reserves $this->foodReserves. " . Emoji::SLIGHTLY_FROWNING_FACE . "\n";
        }
    }

    public function pet(): void {
        $this->happiness += 15;
        echo "You pet $this->name and their happiness is {$this->happiness}. " . Emoji::SMILING_FACE_WITH_HEARTS . "\n";
    }
}

$animalList = [
    new Animal('Dog', 50, 'dogfood'),
    new Animal('Cat', 50, 'catfood'),
    new Animal('Bird', 50, 'birdfood')
];

$zookeeper = new Zookeeper($animalList);

function showMenu(): void {
    echo "\nMenu:\n";
    echo "1. Check Animal Status\n";
    echo "2. Play with Animal\n";
    echo "3. Feed Animal\n";
    echo "4. Pet Animal\n";
    echo "5. Make Animal Work\n";
    echo "6. Exit\n";
}

function getAnimal(Zookeeper $zookeeper): ?Animal {
    $name = readline("Enter the name of the animal: ");
    $animal = $zookeeper->selectAnimal($name);
    if ($animal) {
        echo "You selected {$animal->name}. " . Emoji::SMILING_FACE . "\n";
    }
    return $animal;
}

while (true) {
    showMenu();
    $option = readline("Choose an option: ");

    switch ($option) {
        case '1':
            $animal = getAnimal($zookeeper);
            if ($animal) {
                echo "{$animal->name} has happiness of {$animal->happiness}, food reserves - {$animal->foodReserves}.\n";
                echo "Last played with: " . $animal->lastPlayTime->diffForHumans() . "\n";
                echo "Last fed: " . $animal->lastFeedTime->diffForHumans() . "\n";
            }
            break;
        case '2':
            $animal = getAnimal($zookeeper);
            if ($animal) {
                $seconds = (int)readline("How long do you want to play (in seconds) {$animal->name}? ");
                $animal->play($seconds);
            }
            break;
        case '3':
            $animal = getAnimal($zookeeper);
            if ($animal) {
                $food = readline("What food do you want to give to {$animal->name}? ");
                $animal->feed($food);
            }
            break;
        case '4':
            $animal = getAnimal($zookeeper);
            if ($animal) {
                $animal->pet();
            }
            break;
        case '5':
            $animal = getAnimal($zookeeper);
            if ($animal) {
                $animal->work();
            }
            break;
        case '6':
            echo "Exiting the game.\n";
            exit;
        default:
            echo "Error: Please choose again.\n";
    }
}
