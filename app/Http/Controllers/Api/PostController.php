<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/posts",
     *      operationId="getPostsList",
     *      tags={"posts"},
     *      summary="Get list of posts",
     *      description="Returns list of posts",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(
     *                      property="id",
     *                      type="integer"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function index()
    {
        // Get all posts
        $posts = Post::latest()->paginate(5);

        // Return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    /**
     * @OA\Post(
     *      path="/api/posts",
     *      operationId="storePosts",
     *      tags={"posts"},
     *      summary="Store new posts",
     *      description="Returns posts data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"title", "content", "image"},
     *                  @OA\Property(
     *                      property="title",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary",
     *                      description="Image file to upload"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Data Post Berhasil Ditambahkan!",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="id",
     *                      type="integer"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // Create post
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        // Return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    /**
     * @OA\Get(
     *      path="/api/posts/{id}",
     *      operationId="getPostById",
     *      tags={"posts"},
     *      summary="Get a single post by ID",
     *      description="Returns a single post data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Post ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"title", "content", "image"},
     *                  @OA\Property(
     *                      property="title",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary",
     *                      description="Image file to upload"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="id",
     *                      type="integer"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Post not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */


    public function show($id)
    {
        // Find post by ID
        $post = Post::find($id);

        // Return single post as a resource
        return new PostResource(true, 'Detail Data Post!', $post);
    }

    /**
 * @OA\Put(
 *      path="/api/posts/{id}",
 *      operationId="updatePost",
 *      tags={"posts"},
 *      summary="Update an existing post",
 *      description="Updates post data by ID",
 *      @OA\Parameter(
 *          name="id",
 *          description="Post ID",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"title", "content"},
 *              @OA\Property(
 *                  property="title",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="content",
 *                  type="string"
 *              ),
 *          ),
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      description="File to upload",
 *                      property="image",
 *                      type="string",
 *                      format="file"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="success",
 *                  type="boolean"
 *              ),
 *              @OA\Property(
 *                  property="message",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="data",
 *                  type="object",
 *                  @OA\Property(
 *                      property="id",
 *                      type="integer"
 *                  ),
 *                  @OA\Property(
 *                      property="title",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="content",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="image",
 *                      type="string"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Post not found"
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Validation error"
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal server error"
 *      )
 * )
 */

    

    public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find post by ID
        $post = Post::find($id);

        // Check if image is not empty
        if ($request->hasFile('image')) {
            // Upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // Delete old image
            Storage::delete('public/posts/' . basename($post->image));

            // Update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        } else {
            // Update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        // Return response
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }


    /**
 * @OA\Delete(
 *      path="/api/posts/{id}",
 *      operationId="deletePost",
 *      tags={"posts"},
 *      summary="Delete a post by ID",
 *      description="Deletes a post from the database",
 *      @OA\Parameter(
 *          name="id",
 *          description="Post ID",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="success",
 *                  type="boolean"
 *              ),
 *              @OA\Property(
 *                  property="message",
 *                  type="string"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Post not found"
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal server error"
 *      )
 * )
 */

    public function destroy($id)
    {
        // Find post by ID
        $post = Post::find($id);

        // Delete image
        Storage::delete('public/posts/' . basename($post->image));

        // Delete post
        $post->delete();

        // Return response
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
