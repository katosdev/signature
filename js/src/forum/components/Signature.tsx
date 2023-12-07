import Component, { ComponentAttrs } from 'flarum/common/Component';
import User from 'flarum/common/models/User';
import app from 'flarum/forum/app';
import type Mithril from 'mithril';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';

interface SignatureAttrs extends ComponentAttrs {
  user: User;
  readonly?: boolean;
}

export default class Signature extends Component<SignatureAttrs> {
  editing: boolean = false;
  loading: boolean = false;
  user!: User;

  oninit(vnode: Mithril.Vnode<SignatureAttrs, this>) {
    super.oninit(vnode);
    this.user = vnode.attrs.user;
    this.editing = !vnode.attrs.readonly && this.user.canEditSignature();
  }

  view() {
    return (
      <div className={`Signature ${this.editing ? 'editing' : ''}`}>
        {this.loading ? (
          <LoadingIndicator />
        ) : this.editing ? (
          <textarea
            className="FormControl"
            placeholder="Edit your signature here"
            rows={3}
            value={this.user.signature()}
            onblur={this.save.bind(this)}
          />
        ) : (
          <div className="Signature-content" onclick={this.edit.bind(this)}>
            {!this.user.signature() && <p>Click to write your signature</p>}
            {m.trust(this.user.signatureHtml())}
          </div>
        )}
      </div>
    );
  }

  edit() {
    if (this.user.canEditSignature()) {
      this.editing = true;
      m.redraw();
    }
  }

  save(event: Event) {
    const textarea = event.target as HTMLTextAreaElement;
    this.loading = true;
    this.user
      .save({ signature: textarea.value })
      .then(() => {
        this.loading = false;
        this.editing = false;
        m.redraw();
      })
      .catch(() => {
        this.loading = false;
        m.redraw();
      });
  }
}
