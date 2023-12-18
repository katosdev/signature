import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from './components/Signature';

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'content', function (content) {
    if (this.attrs.post.user?.()) {
      if (this.attrs.post.user().signature()) {
        content.push(
          <div className="Post-signature">
            <Signature user={this.attrs.post.user()} readonly={true} />
          </div>
        );
      }
    }
  });
}
