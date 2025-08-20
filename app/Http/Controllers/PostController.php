<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class PostController extends Controller
{
    protected const LIMIT = 10;

    public function __construct(protected Post $post)
    {
    }

    /**
     * 게시판 리스트
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        return $this->post
            ->query()
            ->orderByDesc('id')
            ->paginate(self::LIMIT);
    }

    /**
     * 게시글 작성
     *
     * @param PostStoreRequest $request
     * @return JsonResponse
     */
    public function store(PostStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $created = $this->post
                ->query()
                ->create([
                    ...$validated,
                    'password' => Hash::make($validated['password'])
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '데이터가 생성되지 않았습니다.',
                'data' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'code' => 201,
            'message' => '데이터가 성공적으로 등록하였습니다.',
            'data' => $created
        ]);
    }

    /**
     * 게사글 데이터 보기
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);

            return response()->json([
                'code' => 200,
                'message' => '게시글 데이터 조회 성공',
                'data' => $post
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => '게시글 데이터를 찾을 수 없습니다.',
                'data' => []
            ]);
        }
    }

    /**
     * 글 업데이트
     *
     * @param PostUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(PostUpdateRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $items = collect($validated)->except('_method')->all();

        try {
            $post = $this->post->query()->findOrFail($id);

            if (!Hash::check($items['password'], $post['password'])) {
                return response()->json([
                    'code' => 401,
                    'message' => '비밀번호를 다시 확인해주세요.',
                    'data' => $items
                ]);
            }

            $post->update([
                ...$items,
                'password' => $post['password']
            ]);

            return response()->json([
                'code' => 200,
                'message' => '데이터가 성공적으로 수정되었습니다.',
                'data' => $post
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => '데이터를 찾을 수 없습니다.',
                'data' => []
            ]);
        }

    }

    /**
     * 글 삭제
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $password = $request->validate([
            'password' => 'required|string|min:4'
        ])['password'];

        try {
            $post = $this->post->query()->findOrFail($id);

            if (!Hash::check($password, $post['password'])) {
                return response()->json([
                    'code' => 401,
                    'message' => '비밀번호를 다시 확인해주세요.',
                    'data' => ['password' => $password]
                ]);
            }

            $post->delete();

            return response()->json([
                'code' => 200,
                'message' => '글삭제가 완료되었습니다.',
                'data' => $post
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => "글삭제 관련 이슈가 발생하였습니다. 현상 : {$e->getMessage()}",
                'data' => []
            ]);
        }
    }
}
