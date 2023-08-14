<?php

namespace App\Tests\Utils;

use App\Twig\Extension\AppExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{
    protected $mockedCategoryTreeFrontPage;
    protected $mockedCategoryTreeListAdmin;
    protected $mockedCategoryTreeAdminOptionList;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $urlGenerator = $kernel->getContainer()->get('router');
        $tested_classes = [
            'CategoryTreeFrontPage',
            'CategoryTreeListAdmin',
            'CategoryTreeAdminOptionList'
        ];
        foreach ($tested_classes as $class) {
            $name = 'mocked'.$class;
            $this->$name = $this->getMockBuilder('App\Utils\\'.$class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
            $this->$name->urlGenerator = $urlGenerator;
        }

    }

    /**
     * @dataProvider dataForCategoryTreeFrontPage
     */
    public function testCategoryTreeFrontPage(string $string, array $array, int $id): void
    {
        $this->mockedCategoryTreeFrontPage->categoriesArrayFromDb = $array;
        $this->mockedCategoryTreeFrontPage->slugger = new AppExtension;
        $main_parent_id = $this->mockedCategoryTreeFrontPage->getMainParent($id)['id'];
        $array = $this->mockedCategoryTreeFrontPage->buildTree($main_parent_id);
        $this->assertSame($string, $this->mockedCategoryTreeFrontPage->getCategoryList($array));
    }

    /**
     * @dataProvider dataForCategoryTreeAdminOptionList
     */
    public function testCategoryTreeAdminOptionList(array $arrayToCompare, array $arrayFromDb) 
    {
        $this->mockedCategoryTreeAdminOptionList->categoriesArrayFromDb = $arrayFromDb;
        $arrayFromDb = $this->mockedCategoryTreeAdminOptionList->buildTree();
        $this->assertSame($arrayToCompare, $this->mockedCategoryTreeAdminOptionList->getCategoryList($arrayFromDb));
    }

    /**
     * @dataProvider dataForCategoryTreeListAdmin
     */
    public function testCategoryTreeListAdmin(string $string, array $array)
    {
        $this->mockedCategoryTreeListAdmin->categoriesArrayFromDb = $array;
        $array = $this->mockedCategoryTreeListAdmin->buildTree();
        $this->assertSame($string, $this->mockedCategoryTreeListAdmin->getCategoryList($array));
    }

    public function dataForCategoryTreeFrontPage()
    {
        yield [
            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8],
            ],
            1
        ];
        yield [
            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8],
            ],
            6
        ];
        yield [
            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8],
            ],
            8
        ];
        yield [
            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8],
            ],
            14
        ];
    }

    public function dataForCategoryTreeAdminOptionList()
    {
        yield [
            [
                ['name'=>'Electronics','id'=>1],
                ['name'=>'--Computers','id'=>6],
                ['name'=>'----Laptops','id'=>8],
                ['name'=>'------HP','id'=>14]
            ],
            [ 
                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
                ['name'=>'HP','id'=>14, 'parent_id'=>8]
            ]
        ];
    }

    public function dataForCategoryTreeListAdmin()
    {
        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Toys<a href="/admin/su/edit-category/2"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/2">Delete</a></li></ul>',
            [ ['id'=>2,'parent_id'=>null,'name'=>'Toys'] ]
         ];

         yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Toys<a href="/admin/su/edit-category/2"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/2">Delete</a></li><li><i class="fa-li fa fa-arrow-right"></i>  Movies<a href="/admin/su/edit-category/3"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/3">Delete</a></li></ul>',
            [
                ['id'=>2,'parent_id'=>null,'name'=>'Toys'],
                ['id'=>3,'parent_id'=>null,'name'=>'Movies']
            ]
         ];

         yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Toys<a href="/admin/su/edit-category/2"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/2">Delete</a></li><li><i class="fa-li fa fa-arrow-right"></i>  Movies<a href="/admin/su/edit-category/3"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/3">Delete</a><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Horrors<a href="/admin/su/edit-category/4"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/4">Delete</a><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Not so scary<a href="/admin/su/edit-category/5"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/5">Delete</a></li></ul></li></ul></li></ul>',

            [
                ['id'=>2,'parent_id'=>null,'name'=>'Toys'],
                ['id'=>3,'parent_id'=>null,'name'=>'Movies'],
                ['id'=>4,'parent_id'=>3,'name'=>'Horrors'],
                ['id'=>5,'parent_id'=>4,'name'=>'Not so scary']
            ]
         ];
    }
}
