<?php

namespace Tests\Feature\LaravelStations\Station20;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Screen;
use App\Models\Sheet;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ScreenTest extends TestCase
{
    use RefreshDatabase;

    private Movie $movie;
    private Screen $screen1;
    private Screen $screen2;
    private Sheet $sheet1;
    private Sheet $sheet2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $genre = Genre::create(['name' => 'テストジャンル']);

        $this->movie = Movie::create([
            'title' => 'テスト映画',
            'image_url' => 'https://test.com/image.jpg',
            'published_year' => 2024,
            'description' => 'テスト概要',
            'is_showing' => true,
            'genre_id' => $genre->id
        ]);

        $this->screen1 = Screen::create(['name' => 'スクリーン1']);
        $this->screen2 = Screen::create(['name' => 'スクリーン2']);

        $this->sheet1 = Sheet::first();
        $this->sheet2 = Sheet::skip(1)->first();
    }

    public function test異なるスクリーンに対して_重複のない上映日時のスケジュールを作成できる(): void
    {
        $this->post('/admin/movies/' . $this->movie->id . '/schedules/store', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time_date' => '2024-12-10',
            'start_time_time' => '10:00',
            'end_time_date' => '2024-12-10',
            'end_time_time' => '12:00'
        ]);

        $this->post('/admin/movies/' . $this->movie->id . '/schedules/store', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen2->id,
            'start_time_date' => '2024-12-10',
            'start_time_time' => '13:00',
            'end_time_date' => '2024-12-10',
            'end_time_time' => '15:00'
        ])->assertRedirect(route('admin.movies.show', $this->movie->id))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('schedules', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen2->id,
            'start_time' => '2024-12-10 13:00:00',
            'end_time' => '2024-12-10 15:00:00'
        ]);
    }

    public function test同一のスクリーンに対して_重複のない上映日時のスケジュールを作成できる(): void
    {
        $this->post('/admin/movies/' . $this->movie->id . '/schedules/store', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time_date' => '2024-12-10',
            'start_time_time' => '10:00',
            'end_time_date' => '2024-12-10',
            'end_time_time' => '12:00'
        ]);

        $response = $this->post('/admin/movies/' . $this->movie->id . '/schedules/store', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time_date' => '2024-12-10',
            'start_time_time' => '13:00',
            'end_time_date' => '2024-12-10',
            'end_time_time' => '15:00'
        ]);
        $response->assertSessionHasNoErrors();
    }

    public function test異なるスクリーンに対して_重複のある上映日時のスケジュールを作成できる(): void
    {
        $this->post('/admin/movies/' . $this->movie->id . '/schedules/store', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time_date' => '2024-12-10',
            'start_time_time' => '10:00',
            'end_time_date' => '2024-12-10',
            'end_time_time' => '12:00'
        ]);

        $response = $this->post('/admin/movies/' . $this->movie->id . '/schedules/store', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen2->id,
            'start_time_date' => '2024-12-10',
            'start_time_time' => '11:00',
            'end_time_date' => '2024-12-10',
            'end_time_time' => '13:00'
        ]);
        $response->assertSessionHasNoErrors();
    }

    public function test同一スクリーンに対して_重複のある上映日時のスケジュールを作成する場合_バリデーションエラー(): void
    {
        $this->post('/admin/movies/' . $this->movie->id . '/schedules/store', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time_date' => '2024-12-10',
            'start_time_time' => '10:00',
            'end_time_date' => '2024-12-10',
            'end_time_time' => '12:00'
        ]);

        $this->post('/admin/movies/' . $this->movie->id . '/schedules/store', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time_date' => '2024-12-10',
            'start_time_time' => '11:00',
            'end_time_date' => '2024-12-10',
            'end_time_time' => '13:00'
        ])->assertSessionHasErrors('screen_id');

        $this->assertDatabaseMissing('schedules', [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time' => '2024-12-10 11:00:00',
            'end_time' => '2024-12-10 13:00:00'
        ]);
    }

    #[DataProvider('reservationPatternProvider')]
    public function test座席予約が実行できる(
        array $schedule1Data,
        array $schedule2Data,
        array $reservations
    ): void
    {
        $schedule1 = Schedule::create([
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time' => new CarbonImmutable($schedule1Data['start']),
            'end_time' => new CarbonImmutable($schedule1Data['end'])
        ]);

        $schedule2 = Schedule::create([
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen2->id,
            'start_time' => new CarbonImmutable($schedule2Data['start']),
            'end_time' => new CarbonImmutable($schedule2Data['end'])
        ]);

        foreach ($reservations as $reservation) {
            $scheduleId = $reservation['schedule_number'] === 1 ? $schedule1->id : $schedule2->id;
            $response = $this->post('/reservations/store', [
                'schedule_id' => $scheduleId,
                'sheet_id' => $reservation['sheet_number'] === 1 ? $this->sheet1->id : $this->sheet2->id,
                'name' => $reservation['name'],
                'email' => $reservation['email'],
                'date' => '2024-12-10'
            ]);
            $response->assertSessionHasNoErrors();
        }
    }

    public static function reservationPatternProvider(): array
    {
        $users_data = [
            [
                'schedule_number' => 1,
                'sheet_number' => 1,
                'name' => 'Test User 1',
                'email' => 'techbowl1@techbowl.com'
            ],
            [
                'schedule_number' => 2,
                'sheet_number' => 1,
                'name' => 'Test User 2',
                'email' => 'techbowl2@techbowl.com'
            ],
            [
                'schedule_number' => 1,
                'sheet_number' => 2,
                'name' => 'Test User 3',
                'email' => 'techbowl3@techbowl.com'
            ]
        ];

        return [
            '上映日時が重複していない予約パターン' => [
                [
                    'start' => '2024-12-10 10:00:00',
                    'end' => '2024-12-10 12:00:00'
                ],
                [
                    'start' => '2024-12-10 13:00:00',
                    'end' => '2024-12-10 15:00:00'
                ],
                $users_data
            ],
            '上映日時が重複している予約パターン' => [
                [
                    'start' => '2024-12-10 10:00:00',
                    'end' => '2024-12-10 12:00:00'
                ],
                [
                    'start' => '2024-12-10 11:00:00',
                    'end' => '2024-12-10 13:00:00'
                ],
                $users_data
            ]
        ];
    }

    public function testユーザ画面にスクリーン名が表示されていない(): void
    {
        $schedule = Schedule::create([
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen1->id,
            'start_time' => new CarbonImmutable('2024-12-10 10:00:00'),
            'end_time' => new CarbonImmutable('2024-12-10 12:00:00')
        ]);

        $response = $this->get('/movies/' . $this->movie->id);
        $response->assertDontSee('スクリーン1');

        $response = $this->get('/movies/' . $this->movie->id . '/schedules/' . $schedule->id . '/sheets?date=2024-12-10 10:00');
        $response->assertDontSee('スクリーン1');
    }
}
