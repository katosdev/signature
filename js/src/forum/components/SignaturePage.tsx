import UserPage from 'flarum/forum/components/UserPage';
import type Mithril from 'mithril';
import Signature from './Signature';

export default class SignaturePage extends UserPage {
  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);

    this.loadUser(m.route.param('username'));
  }

  content() {
    return (
      <div className="SignaturePage">
        <Signature user={this.user} />
      </div>
    );
  }
}
