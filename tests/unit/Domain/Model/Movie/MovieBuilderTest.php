<?php

namespace Fusani\Movies\Domain\Model\Movie;

use PHPUnit_Framework_TestCase;

/**
 * @covers Fusani\Movies\Domain\Model\Movie\MovieBuilder
 */
class MovieBuilderTest extends PHPUnit_Framework_TestCase
{
    protected $builder;

    public function setup()
    {
        $this->builder = new MovieBuilder();
    }

    public function testBuildWithGuideboxIsMovieWithNoTVrageId()
    {
        $data = array_merge($this->guideBoxMovie(), ['tvrage' => ['tvrage_id' => null]]);

        $movie = $this->builder->buildFromGuidebox($data);

        $this->assertInstanceOf(Movie::class, $movie);

        $interest = $movie->provideMovieInterest();
        $this->assertEquals('movie', $interest['type']);
    }

    public function testBuildWithGuideboxIsAMovieWithFlag()
    {
        $data = array_merge($this->guideBoxMovie(), ['isMovie' => 1]);

        $movie = $this->builder->buildFromGuidebox($data);

        $this->assertInstanceOf(Movie::class, $movie);

        $interest = $movie->provideMovieInterest();
        $this->assertEquals('movie', $interest['type']);
    }

    public function testBuildWithGuideboxIsATvShow()
    {
        $data = array_merge($this->guideBoxMovie(), ['tvrage' => ['tvrage_id' => 15]]);

        $movie = $this->builder->buildFromGuidebox($data);

        $this->assertInstanceOf(Movie::class, $movie);

        $interest = $movie->provideMovieInterest();
        $this->assertEquals('tvshow', $interest['type']);
    }

    public function testBuildWithGuideboxSetPlot()
    {
        $data = array_merge($this->guideBoxMovie(), ['overview' => 'Superheros save the world']);

        $movie = $this->builder->buildFromGuidebox($data);

        $this->assertInstanceOf(Movie::class, $movie);

        $interest = $movie->provideMovieInterest();
        $this->assertEquals('Superheros save the world', $interest['plot']);
    }

    public function testBuildWithGuideboxHasFreeSources()
    {
        $data = array_merge($this->guideBoxMovie(), ['free_web_sources' => [['display_name' => 'Netflix', 'link' => 'www.netflix.com']]]);

        $movie = $this->builder->buildFromGuidebox($data);

        $this->assertInstanceOf(Movie::class, $movie);

        $interest = $movie->provideMovieInterest();
        $this->assertNotEmpty($interest['sources']['free']);
    }

    public function testBuildWithGuideboxHasTvEverywhereSources()
    {
        $data = array_merge($this->guideBoxMovie(), ['tv_everywhere_web_sources' => [['display_name' => 'Netflix', 'link' => 'www.netflix.com']]]);

        $movie = $this->builder->buildFromGuidebox($data);

        $this->assertInstanceOf(Movie::class, $movie);

        $interest = $movie->provideMovieInterest();
        $this->assertNotEmpty($interest['sources']['tvEverywhere']);
    }

    public function testBuildWithGuideboxHasSubscriptionSources()
    {
        $data = array_merge($this->guideBoxMovie(), ['subscription_web_sources' => [['display_name' => 'Netflix', 'link' => 'www.netflix.com']]]);

        $movie = $this->builder->buildFromGuidebox($data);

        $this->assertInstanceOf(Movie::class, $movie);

        $interest = $movie->provideMovieInterest();
        $this->assertNotEmpty($interest['sources']['subscription']);
    }

    public function testBuildWithGuideboxHasPurchaseSources()
    {
        $data = array_merge($this->guideBoxMovie(), ['purchase_web_sources' => [['display_name' => 'Netflix', 'link' => 'www.netflix.com']]]);

        $movie = $this->builder->buildFromGuidebox($data);

        $this->assertInstanceOf(Movie::class, $movie);

        $interest = $movie->provideMovieInterest();
        $this->assertNotEmpty($interest['sources']['purchase']);
    }

    public function testBuildWithOmdbNoPoster()
    {
        $data = [
            'Title' => 'Guardians of the Galaxy',
            'Plot' => 'Superheros save the world',
            'Poster' => 'N/A',
            'Type' => 'movie',
            'Year' => 2014,
            'imdbID' => 15,
        ];

        $expected = [
            'id' => 15,
            'plot' => 'Superheros save the world',
            'poster' => null,
            'sources' => [],
            'title' => 'Guardians of the Galaxy',
            'type' => 'movie',
            'year' => 2014,
        ];

        $movie = $this->builder->buildFromOmdb($data);

        $this->assertNotNull($movie);
        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals($expected, $movie->provideMovieInterest());
    }

    public function testBuildWithOmdbNoPlot()
    {
        $data = [
            'Title' => 'Guardians of the Galaxy',
            'Poster' => 'www.movieposters.com/guardians-of-the-galaxy',
            'Type' => 'movie',
            'Year' => 2014,
            'imdbID' => 15,
        ];

        $expected = [
            'id' => 15,
            'plot' => null,
            'poster' => 'www.movieposters.com/guardians-of-the-galaxy',
            'title' => 'Guardians of the Galaxy',
            'sources' => [],
            'type' => 'movie',
            'year' => 2014,
        ];

        $movie = $this->builder->buildFromOmdb($data);

        $this->assertNotNull($movie);
        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals($expected, $movie->provideMovieInterest());
    }

    public function testBuildWithNetflix()
    {
        $data = [
            'show_id' => 1234,
            'show_title' => 'Guardians of the Galaxy',
            'release_year' => 2014,
            'mediatype' => 0,
            'summary' => 'Superheros save the galaxy',
            'poster' => 'www.movieposters.com/guardians-of-the-galaxy',
        ];

        $expected = [
            'id' => 1234,
            'plot' => 'Superheros save the galaxy',
            'poster' => 'www.movieposters.com/guardians-of-the-galaxy',
            'title' => 'Guardians of the Galaxy',
            'sources' => [],
            'type' => 'movie',
            'year' => 2014,
        ];

        $movie = $this->builder->buildFromNetflix($data);

        $this->assertNotNull($movie);
        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals($expected, $movie->provideMovieInterest());
    }

    protected function guideBoxMovie()
    {
        return [
            'id' => 15,
            'title' => 'Guardians of the Galaxy',
            'release_year' => 2014,
            'poster_120x171' => 'www.movieposters.com',
        ];
    }
}
