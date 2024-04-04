import { startStimulusApp } from '@symfony/stimulus-bridge';
import ScrollTo from '@stimulus-components/scroll-to'
import TextareaAutogrow from 'stimulus-textarea-autogrow'
import ScrollProgress from '@stimulus-components/scroll-progress'
import Popover from '@stimulus-components/popover'
import { defaultSchema } from "@hotwired/stimulus"

const customSchema = {
  ...defaultSchema,
  keyMappings: { ...defaultSchema.keyMappings, space: " " },
}

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));
app.stimulusUseDebug = true;
app.schema = customSchema;
app.register('scroll-to', ScrollTo)
app.register('textarea-autogrow', TextareaAutogrow)
app.register('scroll-progress', ScrollProgress)
app.register('popover', Popover)
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
