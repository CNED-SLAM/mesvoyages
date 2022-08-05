<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of VoyagesControllerTest
 *
 * @author emds
 */
class VoyagesControllerTest extends WebTestCase {
    
    const CHEMIN_VOYAGES = '/voyages';
    
    public function testAccesPage(){
        $client = static::createClient();
        $client->request('GET', self::CHEMIN_VOYAGES);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    public function testContenuPage(){
        $client = static::createClient();
        $crawler = $client->request('GET', self::CHEMIN_VOYAGES);
        $this->assertSelectorTextContains('h1', 'Mes voyages');
        $this->assertSelectorTextContains('th', 'Ville');
        $this->assertCount(4, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Los Angeles');
    }
    
    public function testLinkVille(){
        $client = static::createClient();
        $client->request('GET', self::CHEMIN_VOYAGES);
        // clic sur le lien (le nom d'une ville)
        $client->clickLink('Los Angeles');
        // récupération du résultat du clic
        $response = $client->getResponse();
//        dd($client->getRequest());
        // contrôle si le client existe
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        // récupération de la route et contrôle qu'elle est correcte
        $uri = $client->getRequest()->server->get('REQUEST_URI');
        $this->assertEquals('/voyages/voyage/101', $uri);
    }
    
    public function testFiltreVill(){
        $client = static::createClient();
        $client->request('GET', self::CHEMIN_VOYAGES);
        // simulation de la soumission du formaulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Las Vegas'
        ]);
        // vérifie le nombre de lignes obtenues
        $this->assertCount(1, $crawler->filter('h5'));
        // vérifie si la ville correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Las Vegas');
    }
    
}
