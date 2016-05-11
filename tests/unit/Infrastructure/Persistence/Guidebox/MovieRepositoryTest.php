<?php

namespace Fusani\Movies\Infrastructure\Persistence\Guidebox;

use Fusani\Movies\Domain\Model\Movie;
use Fusani\Movies\Infrastructure\Adapter;
use PHPUnit_Framework_TestCase;

/**
 * @covers Fusani\Movies\Infrastructure\Persistence\Guidebox\MovieRepository
 */
class MovieRepositoryTest extends PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected $repository;

    public function setup()
    {
        $this->adapter = $this->getMockBuilder(Adapter\Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = new MovieRepository($this->adapter);
    }

    public function testManyWithTitleNoMatches()
    {
        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue(['results' => []]));

        $movies = $this->repository->manyWithTitle('Guardians');

        $this->assertEquals([], $movies);
    }

    public function testManyWithTitleMatches()
    {
        $movieData = [
            'results' => [
                [
                    'id' => 15,
                    'title' => 'Guardians of the Galaxy',
                    'release_year' => 2014,
                    'poster_120x171' => 'www.movieposters.com',
                ],
                [
                    'id' => 16,
                    'title' => 'Guardians of the Galaxy II',
                    'release_year' => 2018,
                    'poster_120x171' => 'www.movieposters.com',
                ],
            ],
        ];

        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue($movieData));

        $movies = $this->repository->manyWithTitle('Guardians');

        $this->assertNotEquals([], $movies);

        foreach ($movies as $movie) {
            $this->assertNotNull($movie);
            $this->assertInstanceOf(Movie\Movie::class, $movie);
        }
    }

    public function testManyWithTitleLikeNoMatches()
    {
        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue(['results' => []]));

        $movies = $this->repository->manyWithTitleLike('Guardians');

        $this->assertEquals([], $movies);
    }

    public function testManyWithTitleLikeWithMatches()
    {
        $movieData = [
            'results' => [
                [
                    'id' => 15,
                    'title' => 'Guardians of the Galaxy',
                    'release_year' => 2014,
                    'poster_120x171' => 'www.movieposters.com',
                ],
                [
                    'id' => 16,
                    'title' => 'Guardians of the Galaxy II',
                    'release_year' => 2018,
                    'poster_120x171' => 'www.movieposters.com',
                ],
            ],
        ];

        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue($movieData));

        $movies = $this->repository->manyWithTitleLike('Guardians');

        $this->assertNotEquals([], $movies);

        foreach ($movies as $movie) {
            $this->assertNotNull($movie);
            $this->assertInstanceOf(Movie\Movie::class, $movie);
        }
    }

    public function testOneOfIdNoMovieFound()
    {
        $this->setExpectedException(Movie\NotFoundException::class);
        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue([]));

        $this->repository->oneOfId(15);
    }

    public function testOneOfIdMovieMatch()
    {
        $movieData = [
            'id' => 15,
            'title' => 'Guardians of the Galaxy',
            'release_year' => 2014,
            'poster_120x171' => 'www.movieposters.com',
        ];

        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue($movieData));

        $movie = $this->repository->oneOfId(15);

        $this->assertNotNull($movie);
        $this->assertInstanceOf(Movie\Movie::class, $movie);
    }

    public function testOneOfTitleNoYearWithResults()
    {
        $movieData = [
            'results' => [
                [
                    'id' => 15,
                    'title' => 'Guardians of the Galaxy',
                    'release_year' => 2014,
                    'poster_120x171' => 'www.movieposters.com',
                ],
                [
                    'id' => 16,
                    'title' => 'Guardians of the Galaxy',
                    'release_year' => 2014,
                    'poster_120x171' => 'www.movieposters.com',
                ],
            ],
        ];

        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue($movieData));

        $movie = $this->repository->oneOfTitle('Guardians of the Galaxy');

        $this->assertNotNull($movie);
        $this->assertInstanceOf(Movie\Movie::class, $movie);
        $this->assertEquals(15, $movie->identity());
    }

    public function testOneOfTitleWithYear()
    {
        $movieData = [
            'results' => [
                [
                    'id' => 15,
                    'title' => 'Guardians of the Galaxy',
                    'release_year' => 2014,
                    'poster_120x171' => 'www.movieposters.com',
                ],
                [
                    'id' => 16,
                    'title' => 'Guardians of the Galaxy',
                    'release_year' => 2018,
                    'poster_120x171' => 'www.movieposters.com',
                ],
            ],
        ];

        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue($movieData));

        $movie = $this->repository->oneOfTitle('Guardians of the Galaxy', 2018);

        $this->assertNotNull($movie);
        $this->assertInstanceOf(Movie\Movie::class, $movie);
        $this->assertEquals(16, $movie->identity());
    }

    public function testOneOfTitleWithYearNoMatch()
    {
        $this->setExpectedException(Movie\NotFoundException::class);

        $movieData = [
            'results' => [
                [
                    'id' => 15,
                    'title' => 'Guardians of the Galaxy',
                    'release_year' => 2014,
                    'poster_120x171' => 'www.movieposters.com',
                ],
                [
                    'id' => 16,
                    'title' => 'Guardians of the Galaxy',
                    'release_year' => 2018,
                    'poster_120x171' => 'www.movieposters.com',
                ],
            ],
        ];

        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue($movieData));

        $movie = $this->repository->oneOfTitle('Guardians of the Galaxy', 2017);
    }

    public function testOneOfTitleWithNoMatch()
    {
        $this->setExpectedException(Movie\NotFoundException::class);

        $this->adapter->expects($this->once())
            ->method('get')
            ->will($this->returnValue(['results' => []]));

        $movie = $this->repository->oneOfTitle('Guardians of the Galaxy');
    }
}
