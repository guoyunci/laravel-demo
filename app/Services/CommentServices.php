<?php

namespace App\Services;

use App\Constant;
use App\Models\Comment;
use App\Services\User\UserServices;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class CommentServices extends BaseServices
{
    public function getCommentWithUserInfo($goodsId, $page = 1, $limit = 2): array
    {
        $comments = $this->getCommentByGoodsId($goodsId, $page, $limit);
        $userIds = Arr::pluck($comments->items(), 'user_id');
        $userIds = array_unique($userIds);
        $users = UserServices::getInstance()->getUsers($userIds)->keyBy('id');
        $data = collect($comments->items())->map(function (Comment $comment) use ($users) {
            $user = $users->get($comment->user_id);
            $comment = $comment->toArray();
            $comment['picList'] = $comment['picUrls'];
            $comment = Arr::only($comment, ['id', 'addTime', 'content', 'adminContent', 'picList']);
            $comment['nickname'] = $user->nickname ?? '';
            $comment['avatar'] = $user->avatar ?? '';
            return $comment;
        });
        return ['count' => $comments->total(), 'data' => $data];
    }

    public function getCommentByGoodsId(
        $goodsId,
        $page = 1,
        $limit = 2,
        $sort = 'add_time',
        $order = 'desc'
    ): LengthAwarePaginator {
        return Comment::query()->where('value_id', $goodsId)
            ->where('type', Constant::COMMENT_TYPE_GOODS)
            ->orderBy($sort, $order)
            ->paginate(
                $limit,
                ['*'],
                'page',
                $page
            );
    }
}
