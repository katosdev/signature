import Extend from 'flarum/common/extenders';
import SignaturePage from './components/SignaturePage';
import User from 'flarum/common/models/User';

export default [
  new Extend.Routes() //
    .add('user.signature', '/u/:username/signature', SignaturePage),

  new Extend.Model(User) //
    .attribute<string>('signature')
    .attribute<string>('signatureHtml')
    .attribute<boolean>('canEditSignature')
    .attribute<boolean>('canHaveSignature'),
];
