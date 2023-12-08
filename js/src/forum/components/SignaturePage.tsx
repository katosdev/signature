import UserPage from 'flarum/forum/components/UserPage';
import type Mithril from 'mithril';
import Signature from './Signature';
import ItemList from 'flarum/common/utils/ItemList';
import Button from 'flarum/common/components/Button';
import app from 'flarum/forum/app';
import SignatureState from '../states/SignatureState';

export default class SignaturePage extends UserPage {
  signatureState!: SignatureState;

  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);

    this.loadUser(m.route.param('username'));

    this.signatureState = new SignatureState();
  }

  content() {
    return (
      <div className="SignaturePage">
        <div className="SignaturePage-controls">{this.controlItems().toArray()}</div>
        <Signature user={this.user} state={this.signatureState} />
      </div>
    );
  }

  controlItems(): ItemList<Mithril.Children> {
    const items = new ItemList<Mithril.Children>();

    this.user?.canEditSignature() &&
      items.add(
        'edit',
        <Button className="Button" icon="fas fa-edit" onclick={() => this.onEdit()}>
          {app.translator.trans('signature.forum.buttons.edit')}
        </Button>
      );

    return items;
  }

  onEdit() {
    this.signatureState.toggleEditing();
  }
}
