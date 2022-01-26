<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Subcategory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    const MIN_OF_PRODUCT_PER_CATEGORY = 5;
    const MAX_OF_PRODUCT_PER_CATEGORY = 12;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $slugger = new AsciiSlugger();

        $categories = [
            "homme",
            "femme",
            "enfants",
            "accessoires"
        ];

        $subcategories = [
            "homme" => [
                "t-shirt",
                "débardeur",
                "chemise & polo",
                "boardshort",
                "pull"
            ],
            "femme" => [
                "bikini",
                "robe",
                "sportwear"
            ],
            "enfants" => [
                "garçon de 2 à 12 ans",
                "fille de 2 à 12 ans"
            ],
            "accessoires" => [
                "autocollant",
                "masque COVID",
                "coque pour téléphone",
                "serviette de plage",
                "casquettes",
                "autre..."
            ]
        ];

        // Création des catégories
        foreach ($categories as $category) {
            $new_category = new Category();
            $new_category
                ->setName($category)
                ->setSlug($slugger->slug(strtolower($category)));
            // Création des sous catégories associées
            foreach ($subcategories[$category] as $subcategory) {
                /** @var Subcategory $new_subcategory */
                $new_subcategory = new Subcategory();
                $new_subcategory
                    ->setName($subcategory)
                    ->setCategory($new_category)
                    ->setSlug($slugger->slug(strtolower($subcategory)));

                // Création des produits
                for ($i = 0; $i <= $faker->numberBetween(self::MIN_OF_PRODUCT_PER_CATEGORY, self::MAX_OF_PRODUCT_PER_CATEGORY); $i++) {
                    $product = new Product();
                    $product
                        ->setName($faker->sentence(3))
                        ->setPrice($faker->randomFloat(2, 5, 75))
                        ->setDescription($faker->text($faker->numberBetween(10, 50)))
                        ->setQuantityLeft($faker->numberBetween(10, 500))
                        ->setIsPublished($faker->boolean())
                        ->setCategory($new_subcategory)
                        ->setSlug($slugger->slug(strtolower($product->getName())));

                    $manager->persist($product);
                }
                $manager->persist($new_subcategory);
            }
            $manager->persist($new_category);
        }
        $manager->flush();
    }
}
