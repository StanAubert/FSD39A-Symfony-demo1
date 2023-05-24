<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger){
        $this->slugger = $slugger;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for($i = 0 ; $i <= 15 ; $i++){
            $product = new Product();
            $product->setName($faker->sentence(3))->setDescription($faker->text(120))->setPrice($faker->randomFloat(2, 2, 2000))->setCreatedAt(new \DateTimeImmutable)->setPicture($faker->imageUrl(400,400))
            ;
            $slug = $this->slugger->slug($product->getName());
            $product->setSlug($slug);


            $category = new Category;
            $category->setName($faker->word())->setDescription($faker->text(120))->setPicture($faker->imageUrl(300,300));
            $slug = $this->slugger->slug($category->getName());
            $category->setSlug($slug);

            $product->setCategory($category);
            $category->addProduct($product);

            $manager->persist($product);
            $manager->persist($category);
        }


        $manager->flush();
    }
}
