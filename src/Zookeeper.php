<?php
namespace Zookeeper;

use Animal;

class Zookeeper {
    private array $animals;

    public function __construct(array $animals) {
        $this->animals = [];
        foreach ($animals as $animal) {
            $this->addAnimal($animal);
        }
    }

    private function addAnimal(Animal $animal): void {
        $this->animals[$animal->name] = $animal;
        echo "Added {$animal->name} to the zoo.\n";
    }

    public function selectAnimal(string $name): ?Animal {
        if (isset($this->animals[$name])) {
            return $this->animals[$name];
        } else {
            echo "Animal was not found.\n";
            return null;
        }
    }
}
