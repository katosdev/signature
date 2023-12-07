import Component, { ComponentAttrs } from 'flarum/common/Component';
import User from 'flarum/common/models/User';
import app from 'flarum/forum/app';
import TextEditor from 'flarum/common/components/TextEditor';
import Button from 'flarum/common/components/Button';
import Stream from 'flarum/common/utils/Stream';
import SignatureState from '../states/SignatureState';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import type Mithril from 'mithril';

interface SignatureAttrs extends ComponentAttrs {
  user: User;
  readonly?: boolean;
}

export default class Signature extends Component<SignatureAttrs> {
  signatureState!: SignatureState;
  user!: User;
  loading: boolean = false;

  oninit(vnode: Mithril.Vnode<SignatureAttrs, this>) {
    super.oninit(vnode);
    this.user = vnode.attrs.user;
    this.signatureState = new SignatureState();
    this.signatureState.setContent(Stream(this.user.signature() || ''));
  }

  view() {
    return (
      <div className={`Signature ${this.signatureState.editing ? 'editing' : ''}`}>
        {this.loading ? <LoadingIndicator /> : this.signatureState.editing ? this.renderEditor() : this.renderSignature()}
      </div>
    );
  }

  renderEditor() {
    return (
      <div class="SignatureEditor">
        <TextEditor
          value={this.signatureState.content()}
          onchange={this.signatureState.content}
          placeholder="Edit your signature here"
          composer={this.signatureState}
          submitLabel={app.translator.trans('signature.forum.buttons.save')}
        />
      </div>
    );
  }

  renderSignature() {
    return (
      <div className="Signature-content" onclick={this.edit.bind(this)}>
        {!this.user.signature() ? <p>Click to write your signature</p> : m.trust(this.user.signatureHtml())}
      </div>
    );
  }

  edit() {
    if (this.user.canEditSignature()) {
      this.signatureState.toggleEditing();
      m.redraw();
    }
  }

  save() {
    const signature = this.signatureState.content();
    this.loading = true;

    this.user
      .save({ signature })
      .then(() => {
        this.loading = false;
        this.signatureState.setContent(Stream(signature));
        this.signatureState.toggleEditing();
        m.redraw();
      })
      .catch((error) => {
        this.loading = false;
        // Provide specific feedback to the user
        console.error(error);
        alert('Error saving signature'); // Replace with a more user-friendly error handling
        m.redraw();
      });
  }
}
