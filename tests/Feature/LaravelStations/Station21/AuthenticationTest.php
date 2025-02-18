<?php

namespace Tests\Feature\LaravelStations\Station21;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Screen;
use App\Models\Sheet;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private Movie $movie;
    private Schedule $schedule;
    private User $user;
    private array $routePlaceholders;

    protected function setUp(): void
    {
        parent::setUp();

        // SheetTableSeederでシートを作成
        $this->seed(\SheetTableSeeder::class);

        $genre = Genre::create(['name' => 'テストジャンル']);
        $screen = Screen::create(['name' => 'スクリーン1']);


        $this->movie = Movie::create([
            'title' => 'テスト映画',
            'image_url' => 'https://test.com/image.jpg',
            'published_year' => 2024,
            'description' => 'テスト概要',
            'is_showing' => true,
            'genre_id' => $genre->id
        ]);

        $this->schedule = Schedule::create([
            'movie_id' => $this->movie->id,
            'screen_id' => $screen->id,
            'start_time' => new CarbonImmutable('2024-12-10 10:00:00'),
            'end_time' => new CarbonImmutable('2024-12-10 12:00:00')
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@techbowl.com',
            'password' => bcrypt('password123'),
        ]);

        $sheet = Sheet::first();

        // プレースホルダーの設定
        $this->routePlaceholders = [
            '{movie}' => $this->movie->id,
            '{schedule}' => $this->schedule->id,
            '{sheet}' => $sheet->id
        ];
    }

    #[DataProvider('unAuthenticatedRouteProvider')]
    public function test非ログイン時に認証が必要なページがアクセス制限され_loginページにリダイレクトされる(string $route): void
    {
        // プレースホルダーを実際の値に置換
        $route = strtr($route, $this->routePlaceholders);

        $response = $this->get($route);
        $response->assertRedirect('/login');
    }

    public static function unAuthenticatedRouteProvider(): array
    {
        return [
            '映画一覧ページ' => ['/movies'],
            '映画詳細ページ' => ['/movies/{movie}'],
            'シート一覧ページ' => ['/sheets'],
            'スケジュール別シート一覧ページ' => ['/movies/{movie}/schedules/{schedule}/sheets'],
            '予約作成ページ' => ['/movies/{movie}/schedules/{schedule}/reservations/create']
        ];
    }

    #[DataProvider('unAuthenticatedPostRouteProvider')]
    public function test非ログイン時に認証が必要なPOSTリクエストがアクセス制限され_loginページにリダイレクトされる(string $route, array $params): void
    {
        // プレースホルダーを実際の値に置換
        $route = strtr($route, $this->routePlaceholders);
        $params = array_map(function ($value) {
            return is_string($value) ? strtr($value, $this->routePlaceholders) : $value;
        }, $params);

        $response = $this->post($route, $params);
        $response->assertRedirect('/login');
    }

    public static function unAuthenticatedPostRouteProvider(): array
    {
        return [
            '予約保存処理' => [
                '/reservations/store',
                [
                    'schedule_id' => '{schedule}',
                    'sheet_id' => '{sheet}',
                    'date' => '2024-12-10'
                ]
            ]
        ];
    }

    public function testユーザー登録画面が表示され_入力欄がすべて表示されている(): void
    {
        $response = $this->get('/users/create');
        $response->assertStatus(200);
        $response->assertSee('name');
        $response->assertSee('email');
        $response->assertSee('password');
        $response->assertSee('password_confirmation');
    }

    public function testユーザ情報に空欄がある場合_ユーザ登録されない(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ]);
        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertDatabaseCount('users', 1); // setUp()で作成した1件のみ
        $this->assertDatabaseMissing('users', [
            'name' => '',
            'email' => ''
        ]);
    }

    public function test_passwordとpassword_confirmationが一致しない場合_ユーザ登録されない(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test2@techbowl.com',
            'password' => 'password123',
            'password_confirmation' => 'wrongpassword'
        ]);
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', [
            'name' => 'Test User',
            'email' => 'test2@techbowl.com'
        ]);
    }

    public function test正常にユーザ登録ができる(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test2@techbowl.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test2@techbowl.com'
        ]);
    }

    public function test登録したアカウントでログインすることができる(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@techbowl.com',
            'password' => 'password123'
        ]);
        $this->assertAuthenticated();
    }

    public function test予約ページに名前とメールアドレスの入力欄がない(): void
    {
        $this->actingAs($this->user);
        $response = $this->get('/movies/' . $this->movie->id . '/schedules/' . $this->schedule->id . '/reservations/create?date=2024-12-10 10:00&sheetId=' . Sheet::first()->id);
        $response->assertDontSee('name="name"');
        $response->assertDontSee('name="email"');
    }
}
