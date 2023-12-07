import app from 'flarum/forum/app';
import UserPage from 'flarum/forum/components/UserPage';
import type Mithril from 'mithril';
import Signature from './Signature';

export default class SignaturePage extends UserPage {
  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);

    const user = app.session.user;
    if (user) {
      this.loadUser(user.username());
    }
  }

  content() {
    return (
      <div className="SignaturePage">
        <Signature user={this.user} />
      </div>
    );
  }
}
