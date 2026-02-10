<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WordPressContentSeeder extends Seeder
{
    public function run(): void
    {
        $wp = fn (string $table) => DB::connection('wordpress')->table($table);

        // ── 1. Clean up placeholder posts ────────────────────────────────────
        $placeholderIds = $wp('posts')
            ->where('post_status', 'publish')
            ->where('post_type', 'post')
            ->pluck('ID')
            ->toArray();

        if ($placeholderIds) {
            $wp('term_relationships')->whereIn('object_id', $placeholderIds)->delete();
            $wp('postmeta')->whereIn('post_id', $placeholderIds)->delete();
            $wp('posts')->whereIn('ID', $placeholderIds)->delete();
        }

        // ── 2. Update admin to a real author ─────────────────────────────────
        $adminId = $wp('users')->where('user_login', 'admin')->value('ID');
        $wp('users')->where('ID', $adminId)->update([
            'display_name' => 'Sarah Mitchell',
            'user_email' => 'sarah@example.com',
        ]);
        $wp('usermeta')->where('user_id', $adminId)->where('meta_key', 'nickname')->update(['meta_value' => 'Sarah Mitchell']);

        // ── 3. Create two additional authors ─────────────────────────────────
        $authorIds = [$adminId];

        foreach ([
            ['user_login' => 'james_park',   'display_name' => 'James Park',  'user_email' => 'james@example.com'],
            ['user_login' => 'elena_rossi',  'display_name' => 'Elena Rossi', 'user_email' => 'elena@example.com'],
        ] as $author) {
            $existing = $wp('users')->where('user_login', $author['user_login'])->value('ID');

            if ($existing) {
                $authorIds[] = $existing;
            } else {
                $now = now()->toDateTimeString();
                $id = $wp('users')->insertGetId([
                    'user_login' => $author['user_login'],
                    'user_pass' => md5(Str::random(24)),
                    'user_nicename' => $author['user_login'],
                    'user_email' => $author['user_email'],
                    'display_name' => $author['display_name'],
                    'user_registered' => $now,
                    'user_status' => 0,
                ]);

                foreach ([
                    'nickname' => $author['display_name'],
                    'first_name' => explode(' ', $author['display_name'])[0],
                    'last_name' => explode(' ', $author['display_name'])[1] ?? '',
                    'wp_capabilities' => 'a:1:{s:6:"author";b:1;}',
                    'wp_user_level' => '2',
                ] as $key => $value) {
                    $wp('usermeta')->insert(['user_id' => $id, 'meta_key' => $key, 'meta_value' => $value]);
                }

                $authorIds[] = $id;
            }
        }

        // ── 4. Replace categories & tags ─────────────────────────────────────
        // Wipe old terms (leave built-in uncategorized if needed by WP)
        $oldTagSlugs = ['php', 'js', 'node'];
        $oldTermIds = $wp('terms')->whereIn('slug', $oldTagSlugs)->pluck('term_id');
        if ($oldTermIds->isNotEmpty()) {
            $ttIds = $wp('term_taxonomy')->whereIn('term_id', $oldTermIds)->pluck('term_taxonomy_id');
            $wp('term_relationships')->whereIn('term_taxonomy_id', $ttIds)->delete();
            $wp('term_taxonomy')->whereIn('term_id', $oldTermIds)->delete();
            $wp('terms')->whereIn('term_id', $oldTermIds)->delete();
        }

        $categories = ['User Research', 'Design Systems', 'Prototyping', 'Accessibility', 'Visual Design'];
        $tags = ['Figma', 'UX Writing', 'Wireframing', 'User Testing', 'Design Tokens', 'Information Architecture', 'Interaction Design'];

        $categoryIds = [];
        foreach ($categories as $name) {
            $slug = Str::slug($name);
            $termId = $wp('terms')->where('slug', $slug)->value('term_id');
            if (! $termId) {
                $termId = $wp('terms')->insertGetId(['name' => $name, 'slug' => $slug, 'term_group' => 0]);
                $wp('term_taxonomy')->insert(['term_id' => $termId, 'taxonomy' => 'category', 'description' => '', 'parent' => 0, 'count' => 0]);
            }
            $ttId = $wp('term_taxonomy')->where('term_id', $termId)->where('taxonomy', 'category')->value('term_taxonomy_id');
            $categoryIds[$slug] = $ttId;
        }

        $tagIds = [];
        foreach ($tags as $name) {
            $slug = Str::slug($name);
            $termId = $wp('terms')->where('slug', $slug)->value('term_id');
            if (! $termId) {
                $termId = $wp('terms')->insertGetId(['name' => $name, 'slug' => $slug, 'term_group' => 0]);
                $wp('term_taxonomy')->insert(['term_id' => $termId, 'taxonomy' => 'post_tag', 'description' => '', 'parent' => 0, 'count' => 0]);
            }
            $ttId = $wp('term_taxonomy')->where('term_id', $termId)->where('taxonomy', 'post_tag')->value('term_taxonomy_id');
            $tagIds[$slug] = $ttId;
        }

        // ── 5. Post data ──────────────────────────────────────────────────────
        $posts = [
            [
                'title' => 'How to Run Your First User Research Session',
                'slug' => 'how-to-run-your-first-user-research-session',
                'excerpt' => 'User research sounds intimidating, but your first session can be simple, scrappy, and incredibly revealing.',
                'category' => 'user-research',
                'tags' => ['wireframing', 'user-testing'],
                'content' => '<p>User research is one of the most powerful tools in a designer\'s toolkit, yet it\'s often skipped because it feels complicated or time-consuming. The good news is that your first session doesn\'t need to be perfect — it just needs to happen.</p><h2>Start with a clear goal</h2><p>Before recruiting participants, write down one or two questions you want answered. "Why do users abandon the checkout flow?" is a great starting point. Avoid vague goals like "understand users better" — specificity leads to actionable insights.</p><h2>Recruit the right people</h2><p>Five participants will surface most usability issues. Use screener surveys to find people who match your target persona. Reach out through social media, existing customers, or platforms like UserTesting and Respondent.</p><h2>Prepare a simple discussion guide</h2><p>Write 5–8 open-ended questions. Start broad ("Tell me about the last time you bought something online") then get specific. Avoid leading questions that hint at the answer you want.</p><h2>During the session</h2><p>Stay curious, not evaluative. Say "Tell me more about that" frequently. Silence is your friend — let participants fill it. Record the session with permission so you can focus on listening rather than note-taking.</p><h2>Synthesise quickly</h2><p>Within 24 hours, write up key observations. Look for patterns across participants. Two or more people sharing the same pain point is a signal worth acting on.</p><p>Your first research session will be messy. That\'s fine. The goal isn\'t perfection — it\'s getting out of the building and talking to real humans.</p>',
            ],
            [
                'title' => 'Design Tokens: The Glue Between Design and Code',
                'slug' => 'design-tokens-the-glue-between-design-and-code',
                'excerpt' => 'Design tokens give your team a shared language for colour, spacing, and typography — and they eliminate the "which shade of grey?" debate forever.',
                'category' => 'design-systems',
                'tags' => ['design-tokens', 'figma'],
                'content' => '<p>If you\'ve ever heard a developer ask "which blue is this — the dark one or the medium one?" you understand the pain that design tokens solve. Tokens are named design decisions: <code>color.brand.primary</code> instead of <code>#3B5BDB</code>.</p><h2>What are design tokens?</h2><p>A design token is a key-value pair that stores a design decision in a platform-agnostic format. Instead of hard-coding a hex value, you reference a token. If the brand colour changes, you update one token — and every platform inherits the change automatically.</p><h2>Categories of tokens</h2><p>Tokens typically cover colour, typography (font size, weight, line height), spacing, border radius, shadow, and animation timing. Some teams add semantic tokens on top — tokens that reference other tokens, like <code>color.text.primary</code> pointing to <code>color.neutral.900</code>.</p><h2>Setting up tokens in Figma</h2><p>Use the Tokens Studio plugin to define and sync tokens. Connect it to a Git repository so that designers and developers always reference the same source of truth. When a designer updates a token value, opening a pull request makes the change reviewable before it ships.</p><h2>Consuming tokens in code</h2><p>Style Dictionary from Amazon is the go-to tool for transforming tokens into CSS custom properties, JavaScript objects, or platform-specific formats. Configure a build step that runs whenever tokens change.</p><p>Design tokens are not glamorous. But they are the invisible infrastructure that lets design systems scale without chaos.</p>',
            ],
            [
                'title' => 'Prototyping Fidelity: When Low Is Better Than High',
                'slug' => 'prototyping-fidelity-when-low-is-better-than-high',
                'excerpt' => 'High-fidelity prototypes look impressive in presentations, but low-fidelity prototypes often teach you more, faster.',
                'category' => 'prototyping',
                'tags' => ['wireframing', 'user-testing', 'figma'],
                'content' => '<p>There\'s a persistent myth in product design that a polished prototype signals professionalism. In reality, high-fidelity too early is one of the most common and costly mistakes teams make.</p><h2>The fidelity spectrum</h2><p>Fidelity refers to how closely a prototype resembles the final product. Paper sketches and rough wireframes sit at the low end. Pixel-perfect clickable prototypes sit at the high end. Each serves a different purpose.</p><h2>When to go low</h2><p>Early-stage exploration benefits enormously from low fidelity. Sketching on paper takes minutes. If a concept fails in testing, there\'s no emotional attachment to polished assets. Participants also give more honest feedback to rough prototypes — they\'re less likely to comment on button colour when it\'s drawn in biro.</p><h2>When to go high</h2><p>High-fidelity shines when testing microcopy, validating visual hierarchy, or preparing for stakeholder sign-off. Once the information architecture is solid and flows are agreed upon, the investment in polish pays off.</p><h2>The middle ground: mid-fi</h2><p>Many teams find a mid-fidelity sweet spot — grayscale wireframes with real copy and basic interactions. This is fast enough to iterate quickly but detailed enough to communicate intent clearly to developers.</p><p>Match your fidelity to your question. Testing navigation structure? Grab a marker. Testing if users understand a modal\'s purpose? Open Figma.</p>',
            ],
            [
                'title' => 'Writing Accessible Alt Text: A Practical Guide',
                'slug' => 'writing-accessible-alt-text-a-practical-guide',
                'excerpt' => 'Alt text is a tiny piece of copy with outsized impact. Here\'s how to write it well every time.',
                'category' => 'accessibility',
                'tags' => ['ux-writing'],
                'content' => '<p>Alt text — the text alternative for images — is one of the most frequently misunderstood elements of accessible design. Done badly, it\'s useless. Done well, it gives screen reader users the same experience as sighted users.</p><h2>The golden rule</h2><p>Describe the function or content of the image, not just what it literally depicts. An image of a magnifying glass in a search bar should read "Search", not "Magnifying glass icon". Context determines meaning.</p><h2>Decorative images</h2><p>If an image is purely decorative — a background texture, a divider illustration — use an empty alt attribute: <code>alt=""</code>. Screen readers will skip it entirely, which is exactly what you want.</p><h2>Complex images</h2><p>Charts, graphs, and infographics need more than a short description. Provide a long description either in the alt attribute or in adjacent body text. "Bar chart showing monthly revenue growth from January to December 2024, peaking at $1.2M in October" tells users what they need to know.</p><h2>Avoid redundancy</h2><p>Don\'t start alt text with "Image of..." or "Picture of..." — screen readers already announce it as an image. Get straight to the content.</p><h2>Test with a screen reader</h2><p>VoiceOver on macOS and NVDA on Windows are free. Spend 30 minutes navigating your site with a screen reader. The experience will permanently change how you write alt text.</p>',
            ],
            [
                'title' => 'Building a Component Library That People Actually Use',
                'slug' => 'building-a-component-library-that-people-actually-use',
                'excerpt' => 'Most component libraries die from neglect. Here\'s how to build one with adoption built in from day one.',
                'category' => 'design-systems',
                'tags' => ['design-tokens', 'figma', 'information-architecture'],
                'content' => '<p>A component library is only valuable if people use it. Yet countless libraries are built with great fanfare and then quietly abandoned within six months. The difference between libraries that stick and those that don\'t is rarely the components themselves — it\'s the process.</p><h2>Start with an audit</h2><p>Before building anything new, catalogue what already exists. Take screenshots of every UI component across your product. Group similar ones together. You\'ll likely find 12 variations of a button where 3 would suffice. That audit becomes your to-do list.</p><h2>Build with consumers, not for them</h2><p>Invite engineers and other designers into the process from day one. Run co-creation workshops to define component APIs. If the people who will use the library helped build it, they\'re invested in its success.</p><h2>Document as you go</h2><p>Component documentation written after the fact is always incomplete. Write usage guidelines, do/don\'t examples, and accessibility notes alongside the component. Storybook makes this easy by co-locating docs with code.</p><h2>Establish a governance model</h2><p>Who can contribute? Who reviews changes? Who decides when to deprecate? Without governance, libraries drift. A lightweight RFC (Request for Comments) process — even just a Slack channel or GitHub discussion — keeps changes intentional.</p><h2>Measure adoption</h2><p>Track which components are used in production and which aren\'t. Low-adoption components need investigation: are they hard to use, undocumented, or simply solving a problem that doesn\'t exist?</p>',
            ],
            [
                'title' => 'The Art of the UX Audit: Finding Problems Before Users Do',
                'slug' => 'the-art-of-the-ux-audit-finding-problems-before-users-do',
                'excerpt' => 'A UX audit is a structured review of your product against established heuristics and your own design principles.',
                'category' => 'user-research',
                'tags' => ['user-testing', 'information-architecture'],
                'content' => '<p>A UX audit — sometimes called a heuristic evaluation — is a method for identifying usability problems without recruiting participants. An experienced evaluator reviews the product against a set of principles and flags issues. It\'s fast, affordable, and often reveals problems that user testing misses.</p><h2>Choose your heuristics</h2><p>Nielsen\'s 10 Usability Heuristics remain the gold standard. They cover everything from error prevention to user control. If your product has bespoke design principles, evaluate against those too — they\'re promises your team made to users.</p><h2>Review as a user, not a designer</h2><p>This is harder than it sounds. You\'ve seen the interface hundreds of times. Force yourself into first-time-user mode by reviewing flows end to end, not screen by screen. Starting from the landing page and completing a task often reveals gaps that screen-by-screen review misses.</p><h2>Severity ratings</h2><p>Not all issues are equal. Rate each finding on a 1–4 scale: cosmetic (1), minor (2), major (3), catastrophic (4). Focus remediation effort on 3s and 4s first. A severity rating also helps prioritise with product managers who are weighing UX debt against feature work.</p><h2>Deliver findings as a story</h2><p>A list of 47 issues is overwhelming. Group findings by theme — navigation, forms, feedback, empty states — and lead with the top three most impactful problems. Show screenshots side by side with annotated recommendations.</p>',
            ],
            [
                'title' => 'Micro-interactions: The Details That Make Products Feel Alive',
                'slug' => 'micro-interactions-the-details-that-make-products-feel-alive',
                'excerpt' => 'Micro-interactions are the tiny moments of feedback that tell users their actions had an effect. They\'re easy to overlook and hard to overstate.',
                'category' => 'visual-design',
                'tags' => ['interaction-design', 'figma'],
                'content' => '<p>When you toggle a switch and it animates smoothly from off to on, that\'s a micro-interaction. When you pull down to refresh and a subtle spinner appears, that\'s a micro-interaction. These moments are small in isolation but collectively define whether a product feels polished or rough.</p><h2>The four parts of a micro-interaction</h2><p>Dan Saffer\'s framework breaks every micro-interaction into a trigger, rules, feedback, and loops/modes. The trigger starts it (a tap, a scroll, a system event). The rules define what happens. The feedback communicates the result. Loops and modes determine how the interaction behaves over time.</p><h2>Where micro-interactions matter most</h2><p>Form validation, button click states, loading indicators, success confirmations, and error messages are the highest-impact candidates. A form field that turns red with a helpful message is a micro-interaction. A button that briefly scales down on tap gives confidence the press registered.</p><h2>The danger of over-engineering</h2><p>Micro-interactions should feel invisible — they should enhance without distracting. An animation that takes 600ms where 200ms would do creates friction. Always prototype and test with real users; what feels delightful to a designer can feel slow or confusing to someone unfamiliar with the interface.</p><h2>Tools</h2><p>Figma\'s Smart Animate handles most transition work during prototyping. For production, CSS transitions and the Web Animations API cover the majority of cases. Lottie is useful for complex animations exported from After Effects.</p>',
            ],
            [
                'title' => 'Information Architecture: Organising Content for Humans',
                'slug' => 'information-architecture-organising-content-for-humans',
                'excerpt' => 'Information architecture is the invisible backbone of every well-designed product. Get it right and users never notice. Get it wrong and they\'ll blame themselves.',
                'category' => 'user-research',
                'tags' => ['information-architecture', 'wireframing'],
                'content' => '<p>Information architecture (IA) is the practice of organising, structuring, and labelling content so that users can find it. When IA is good, navigation feels intuitive and obvious. When it\'s bad, users wander, backtrack, and eventually give up — often blaming themselves for being confused.</p><h2>Card sorting</h2><p>Card sorting is the go-to IA research method. Write the names of your content types or features on cards (physical or digital), then ask participants to group them in a way that makes sense to them and name each group. The patterns that emerge reveal your users\' mental models.</p><h2>Tree testing</h2><p>Once you have a proposed navigation structure, tree testing validates whether users can find things in it. Show participants a text-only hierarchy — no visual design — and ask them to find specific items. Success rates flag structural problems before any design work begins.</p><h2>The three pillars: organisation, labelling, navigation</h2><p>Organisation schemes (alphabetical, chronological, by audience, by topic) should match how users think about content, not how your team talks about it internally. Labels should use the vocabulary of users — check analytics and support tickets for the exact words people use. Navigation systems (global, local, contextual) work together to give users multiple pathways to the same content.</p><h2>IA is never done</h2><p>As products grow, their IA drifts. New features are bolted on, sections are renamed, and the original structure no longer fits the content it contains. Budget for regular IA reviews — at least annually for fast-moving products.</p>',
            ],
            [
                'title' => 'Writing UX Copy That Gets Out of the Way',
                'slug' => 'writing-ux-copy-that-gets-out-of-the-way',
                'excerpt' => 'The best interface copy is invisible. Users complete their tasks without noticing the words. Here\'s how to write it.',
                'category' => 'visual-design',
                'tags' => ['ux-writing'],
                'content' => '<p>UX writing — the craft of writing the words in interfaces — is one of the most undervalued design disciplines. A single word change in a button label can increase conversions by double digits. A confusing error message can cost thousands in support tickets. Words are the interface.</p><h2>Clarity over cleverness</h2><p>"Oops! Something went sideways" might feel friendly, but "Unable to save changes — please try again" is actually useful. Clever copy entertains; clear copy helps. Default to clear, then add warmth if the context invites it.</p><h2>Front-load the key information</h2><p>Users scan, not read. The first two words of any label, button, or heading carry the most weight. "Delete account" is better than "If you\'d like to delete your account, click here". Lead with the action or the consequence.</p><h2>Write for error states first</h2><p>Error messages are the most-read copy in any interface, and the most neglected. A good error message has three parts: what went wrong, why it happened (if useful), and what to do next. "Password must contain at least 8 characters" beats "Invalid password" every time.</p><h2>Test your copy</h2><p>Five-second tests are perfect for copy. Show a screen for five seconds and ask what the page is for or what the user should do next. If half your testers can\'t answer, the copy is working against the design.</p>',
            ],
            [
                'title' => 'The Case for Inclusive Design from Day One',
                'slug' => 'the-case-for-inclusive-design-from-day-one',
                'excerpt' => 'Retrofitting accessibility is expensive and never complete. Building inclusively from the start is better for everyone.',
                'category' => 'accessibility',
                'tags' => ['ux-writing', 'design-tokens'],
                'content' => '<p>Accessibility is often treated as a checkbox at the end of a project: "We built the feature, now let\'s make it accessible." This approach leads to band-aid fixes, technical debt, and interfaces that are technically compliant but genuinely unusable by people with disabilities.</p><h2>The curb-cut effect</h2><p>Accessibility improvements benefit everyone, not just users with permanent disabilities. Captions help users in noisy environments. High contrast helps users in bright sunlight. Keyboard navigation helps power users. Designing inclusively from the start lifts the experience for everyone.</p><h2>Colour contrast</h2><p>WCAG 2.1 AA requires a contrast ratio of at least 4.5:1 for normal text and 3:1 for large text. Tools like Colour Contrast Analyser and Figma\'s built-in accessibility checker make it easy to verify ratios during design. Setting contrast requirements in your design tokens enforces this at a system level.</p><h2>Focus states</h2><p>Every interactive element needs a visible focus state — a clear indicator of which element the keyboard currently controls. Default browser focus outlines were historically ugly, so designers removed them. This made keyboard navigation nearly impossible. Design a beautiful, high-contrast custom focus state instead of removing the default.</p><h2>Include users with disabilities in research</h2><p>You cannot design for an experience you\'ve never observed. Recruit participants who use assistive technology — screen readers, switch access, voice control — and watch them use your product. What you learn in a single session will reshape how you design.</p>',
            ],
            [
                'title' => 'Jobs to Be Done: Understanding Why Users Really Hire Your Product',
                'slug' => 'jobs-to-be-done-understanding-why-users-really-hire-your-product',
                'excerpt' => 'Users don\'t buy products — they hire them to get a job done. The Jobs to Be Done framework reframes how you think about user motivation.',
                'category' => 'user-research',
                'tags' => ['user-testing'],
                'content' => '<p>Clayton Christensen introduced the Jobs to Be Done (JTBD) framework with a simple but profound insight: people don\'t buy products for their features — they hire them to make progress in a specific situation. The famous milkshake example: McDonald\'s discovered that most milkshakes were bought in the morning by commuters who needed something to hold, sip slowly, and stave off hunger until lunch. The job was "make my commute less boring and keep me full", not "enjoy a delicious drink".</p><h2>The job statement</h2><p>A JTBD job statement follows this pattern: "When [situation], I want to [motivation], so I can [expected outcome]." It focuses on the context and goal, not the solution. "When I\'m planning a trip, I want to see all my expenses in one place, so I can stay within budget" is a job statement. "I want an expense tracking app" is a feature request.</p><h2>Switch interviews</h2><p>To uncover real jobs, interview recent adopters and recent churners. Ask about the timeline of the decision to switch — what was happening in their life, what was the first thought, what nearly stopped them. The "timeline of purchase" technique surfaces the situational forces that drove the hire.</p><h2>Applying JTBD to product decisions</h2><p>When evaluating features, ask "does this help users make progress in their job?" rather than "is this what users asked for?". Users often request solutions, not jobs. Your role is to understand the underlying progress they\'re trying to make.</p>',
            ],
            [
                'title' => 'Responsive Typography: Scaling Text Without the Headaches',
                'slug' => 'responsive-typography-scaling-text-without-the-headaches',
                'excerpt' => 'Fluid typography adapts gracefully across screen sizes. Here\'s a practical guide to implementing it with CSS clamp and design tokens.',
                'category' => 'visual-design',
                'tags' => ['design-tokens', 'figma'],
                'content' => '<p>Typography that looks elegant on a 27-inch monitor often feels enormous on a phone, and text that\'s perfectly sized for mobile can feel tiny on a wide display. Responsive typography solves this by letting type scale fluidly between a minimum and maximum size based on the viewport width.</p><h2>The old way: breakpoints</h2><p>The traditional approach defined type sizes at specific breakpoints: 16px on mobile, 18px on tablet, 20px on desktop. This worked but required explicit overrides at every breakpoint, and the jumps between sizes could feel abrupt.</p><h2>CSS clamp</h2><p><code>clamp(min, preferred, max)</code> is the modern answer. The preferred value uses viewport units to scale continuously between the min and max. <code>font-size: clamp(1rem, 2.5vw, 1.5rem)</code> ensures text is never smaller than 16px, never larger than 24px, and scales smoothly in between.</p><h2>Design tokens for type scales</h2><p>Define your type scale as tokens — <code>font.size.sm</code>, <code>font.size.base</code>, <code>font.size.lg</code> — and give each a clamp value. This way, every component that uses the token inherits responsive behaviour automatically, without per-component breakpoint overrides.</p><h2>Testing across devices</h2><p>Responsive Typography is one area where DevTools viewport simulation isn\'t enough. Test on real devices, especially cheap Android phones with lower pixel densities and small screens. Text that looks fine in Chrome DevTools can be genuinely unusable on budget hardware.</p>',
            ],
            [
                'title' => 'Designing for Anxiety: Reducing Cognitive Load in Complex Flows',
                'slug' => 'designing-for-anxiety-reducing-cognitive-load-in-complex-flows',
                'excerpt' => 'Complex multi-step flows can overwhelm users. These design patterns reduce cognitive load and keep users moving forward.',
                'category' => 'prototyping',
                'tags' => ['user-testing', 'interaction-design'],
                'content' => '<p>Some tasks are inherently complex: filing a tax return, setting up a new financial account, configuring a software tool. Users arrive already anxious. Good design doesn\'t eliminate that complexity — it manages it, revealing information as needed and removing every unnecessary obstacle from the path.</p><h2>One thing at a time</h2><p>The most effective technique for complex flows is progressive disclosure — showing only what\'s needed for the current step. The one-question-per-page pattern, popularised by GOV.UK, dramatically reduces errors and abandonment. Users can focus entirely on the question in front of them.</p><h2>Show progress</h2><p>Progress indicators reduce anxiety by answering "how much longer?". A step indicator ("Step 3 of 7") or a progress bar gives users a sense of control and a clear finish line. Without progress feedback, users often abandon a flow mid-way assuming it will go on forever.</p><h2>Graceful error recovery</h2><p>In complex flows, errors are inevitable. Design for them explicitly. Inline validation tells users immediately when something is wrong — not after they submit the entire form. Preserve previously entered data. Never make users re-enter information they already provided because one field was invalid.</p><h2>Exit intent handling</h2><p>If users navigate away mid-flow, save their progress. A "You have an incomplete application — continue where you left off" message reduces re-entry friction enormously. This is especially important for longer forms where a single session may not be realistic.</p>',
            ],
            [
                'title' => 'From Sketch to Prototype: A Rapid Ideation Workflow',
                'slug' => 'from-sketch-to-prototype-a-rapid-ideation-workflow',
                'excerpt' => 'A practical six-step workflow for moving from a blank page to a testable prototype in under a day.',
                'category' => 'prototyping',
                'tags' => ['wireframing', 'figma'],
                'content' => '<p>The gap between a design brief and a testable prototype feels enormous when you\'re staring at a blank artboard. A structured ideation workflow compresses that gap — moving from fuzzy problem to something users can interact with in hours rather than days.</p><h2>Step 1: Define the problem</h2><p>Write one sentence that captures what you\'re solving for. "New users can\'t find the sharing feature" is specific enough to act on. Pin it where you can see it throughout the session — it\'s easy to drift into solving the wrong problem when you\'re deep in exploration.</p><h2>Step 2: Crazy 8s</h2><p>Fold a piece of A4 paper into eight panels. Set a timer for eight minutes. Sketch one idea per panel. Don\'t censor yourself — quantity over quality. The constraints (8 ideas, 1 minute each) short-circuit the perfectionism that kills early-stage exploration.</p><h2>Step 3: Three-up refinement</h2><p>Choose your three most promising Crazy 8 ideas and sketch each one in more detail. Add labels, annotations, and flow arrows. These become the input for your wireframe.</p><h2>Step 4: Digital wireframe</h2><p>Translate the best of your three-ups into a grayscale Figma wireframe. Use placeholder text where real copy isn\'t ready. Focus on layout and interaction, not visual styling.</p><h2>Step 5: Add interactions</h2><p>Connect your frames with Figma\'s prototype mode. Cover the primary happy path at minimum — the journey a user takes when everything goes right. Add at least one error state.</p><h2>Step 6: Test</h2><p>Share the prototype link with two or three colleagues or users. Ask them to complete a task without guidance. Watch, don\'t explain. Take notes on where they hesitate or get stuck. You now have a testable artefact and early signal, all in a day\'s work.</p>',
            ],
            [
                'title' => 'The Psychology of Colour in UI Design',
                'slug' => 'the-psychology-of-colour-in-ui-design',
                'excerpt' => 'Colour does more than make a UI look good — it communicates status, guides attention, and shapes emotional response.',
                'category' => 'visual-design',
                'tags' => ['design-tokens', 'figma'],
                'content' => '<p>Colour is one of the first things users perceive and one of the hardest things to change once a product is established. Getting colour right is part aesthetics, part psychology, and part accessibility — and the stakes are high.</p><h2>Semantic colour</h2><p>Semantic colours communicate meaning regardless of brand: green for success, red for error, yellow/amber for warning, blue for information. These expectations are deeply ingrained — violating them (for example, using red for a success state) creates friction and confusion. Reserve non-semantic colour usage for brand and decorative elements.</p><h2>Colour and hierarchy</h2><p>Saturation naturally draws the eye. In a UI, the highest-saturation element becomes the primary focal point. Use this deliberately: a saturated primary button surrounded by desaturated controls creates natural hierarchy without requiring size or position changes.</p><h2>Dark mode colour</h2><p>Dark mode isn\'t simply inverting your light palette. Dark backgrounds should use very dark greys (not true black, which creates harsh contrast) and foreground colours need recalibrating — colours that look vivid and balanced on white can look washed out or neon on dark backgrounds. Build a separate dark token set rather than inverting light tokens.</p><h2>Colour blindness</h2><p>Roughly 8% of men and 0.5% of women have some form of colour vision deficiency. Never use colour alone to convey information — always pair it with a label, icon, or pattern. Figma\'s "colour blindness" view and tools like Stark help you simulate how your palette appears with different types of colour vision deficiency.</p>',
            ],
            [
                'title' => 'Usability Testing on a Shoestring Budget',
                'slug' => 'usability-testing-on-a-shoestring-budget',
                'excerpt' => 'You don\'t need a usability lab or a big budget to run meaningful tests. Here\'s how to get quality insights for free.',
                'category' => 'user-research',
                'tags' => ['user-testing'],
                'content' => '<p>The most common excuse for skipping usability testing is budget. Lab facilities, recruitment services, and incentives add up quickly. But the highest-ROI usability testing isn\'t expensive — it\'s just consistent. Here\'s how to run meaningful sessions with minimal spend.</p><h2>Hallway testing</h2><p>The classic low-budget technique: recruit anyone who walks past (colleagues from other teams, friends, family) and ask them to complete a task in your product. It\'s not rigorous, but five hallway sessions will surface most critical usability problems faster than any other technique.</p><h2>Remote unmoderated testing</h2><p>Tools like Maze, Useberry, and Lyssna offer free tiers that support basic task-based testing. Distribute the link via social media, community forums, or your existing user base. You can collect 20+ responses in 24 hours with zero moderation time.</p><h2>The five-user rule</h2><p>Jakob Nielsen\'s research shows that five users will surface approximately 85% of usability problems. You don\'t need 20 participants to learn what matters. Run five sessions, synthesize, fix the biggest issues, then run five more.</p><h2>Compensate fairly (even cheaply)</h2><p>A £10 gift card dramatically increases recruitment response rates and participant quality. Build a pool of willing testers from your existing users — customers who\'ve opted in to research contact are the easiest and highest-quality source you have.</p><h2>Analyse out loud</h2><p>Watch session recordings with at least one other person from your team. The act of explaining observations out loud surfaces insights faster than solo note review. An hour-long watch party with 5 recordings is worth more than 5 hours of individual analysis.</p>',
            ],
            [
                'title' => 'Grid Systems: The Foundation of Visual Consistency',
                'slug' => 'grid-systems-the-foundation-of-visual-consistency',
                'excerpt' => 'A well-designed grid isn\'t a constraint — it\'s the invisible structure that makes complex layouts feel effortless.',
                'category' => 'visual-design',
                'tags' => ['figma', 'design-tokens'],
                'content' => '<p>The grid is one of the oldest tools in visual design, inherited directly from print and adapted (not always gracefully) for screens. A product without a clear grid looks inconsistent, haphazard, and untrustworthy — even when users can\'t articulate why.</p><h2>Column grids</h2><p>Most web layouts are built on a column grid. The 12-column grid became standard because 12 is divisible by 2, 3, 4, and 6, allowing flexible content arrangements. Columns are defined by width, gutters (the space between columns), and margins (the space between the grid and the viewport edge).</p><h2>8-point grid</h2><p>The 8-point grid system aligns all spacing and sizing decisions to multiples of 8: 8px, 16px, 24px, 32px, 48px, 64px. This creates visual harmony because elements relate to each other through consistent proportional relationships. When every spacing decision comes from the same base unit, inconsistency becomes the exception rather than the norm.</p><h2>Baseline grid</h2><p>A baseline grid aligns text to a horizontal rhythm, just like lined paper. When body text in a sidebar and body text in the main column sit on the same baseline grid, the page feels settled and intentional. This is harder to implement on the web than in print, but CSS leading (line-height) and margin values can approximate it reliably.</p><h2>Breaking the grid</h2><p>Grids exist to be broken — judiciously. An element that bleeds off the edge, overlaps a column boundary, or deliberately misaligns creates visual tension that can draw attention and add energy. The key word is deliberately: breaks only work when the grid is established enough that the violation is noticeable.</p>',
            ],
            [
                'title' => 'Onboarding UX: The First Five Minutes That Define Retention',
                'slug' => 'onboarding-ux-the-first-five-minutes-that-define-retention',
                'excerpt' => 'Users decide whether to return within the first few minutes. Good onboarding design makes those minutes count.',
                'category' => 'prototyping',
                'tags' => ['user-testing', 'interaction-design', 'ux-writing'],
                'content' => '<p>Onboarding is the most important UX you\'ll ever design. Users who successfully complete onboarding are dramatically more likely to become active, paying, and retained customers. Users who don\'t are gone — usually forever. The first five minutes carry a disproportionate amount of weight.</p><h2>Don\'t front-load information</h2><p>The classic onboarding mistake is the feature tour: a modal carousel that explains every feature before the user has used anything. Nobody reads it. Users click "Skip" as fast as possible to get to the actual product. Contextual tips — shown when a user first encounters a feature — are far more effective.</p><h2>Reach the "aha moment" fast</h2><p>Every product has a moment where users realise its value — the aha moment. Slack\'s is sending your first message in a channel. Spotify\'s is hearing a song you didn\'t know you\'d love. Identify yours, then redesign onboarding to get users there as fast as possible. Remove every step that doesn\'t directly contribute.</p><h2>Empty states are onboarding</h2><p>An empty dashboard with no prompts creates anxiety. What do I do first? Empty states should guide the next action — "Create your first project", "Invite a teammate", "Import your data". The best empty states show users what the experience looks like when populated, reducing the uncertainty of starting from scratch.</p><h2>Measure completion, not just starts</h2><p>Track where users drop out of your onboarding flow. A 70% completion rate on step 1 that drops to 20% at step 3 tells you exactly where to focus. Funnel analysis in your analytics tool — or even watching session recordings — will surface the exact friction point.</p>',
            ],
            [
                'title' => 'Designing Dark Mode Without Losing Your Mind',
                'slug' => 'designing-dark-mode-without-losing-your-mind',
                'excerpt' => 'Dark mode is expected by users and complicated to implement well. A structured approach makes it manageable.',
                'category' => 'design-systems',
                'tags' => ['design-tokens', 'figma'],
                'content' => '<p>Dark mode is no longer optional. Users expect it, operating systems support it, and a significant portion of your user base will use it as their primary mode. But bolt-on dark modes are almost always bad — jarring colour combinations, missed components, and colours that were never designed for dark backgrounds. Getting dark mode right requires treating it as a first-class concern from the start.</p><h2>Start with semantic tokens</h2><p>The prerequisite for solid dark mode support is a semantic token layer. Rather than hard-coding <code>#1A1A2E</code>, components reference <code>color.background.primary</code>. That token resolves to a light value by default and a different dark value when the dark mode is active. Swap the token values, not the component code.</p><h2>Elevation and shadow on dark backgrounds</h2><p>On light backgrounds, elevation is communicated through shadows. On dark backgrounds, shadows are invisible. Instead, use varying levels of surface lightness — a modal sits slightly lighter than the page background, a popover lighter still. Material Design\'s elevation system for dark mode is a useful reference.</p><h2>Image handling</h2><p>Images shot on white backgrounds look awkward floating in dark UIs. Where you control the imagery (illustrations, icons), design them for dark backgrounds or make backgrounds transparent. For photos you don\'t control, a subtle vignette, rounded corners, or a slight background tint can help them integrate better.</p><h2>Test, don\'t assume</h2><p>Many designers check dark mode by toggling in Figma and calling it done. Real testing means running your prototype on actual devices in a darkened environment, where OLED screens show true black and any non-black near-black inconsistencies become painfully obvious.</p>',
            ],
            [
                'title' => 'The Role of Cognitive Biases in UX Design',
                'slug' => 'the-role-of-cognitive-biases-in-ux-design',
                'excerpt' => 'Understanding cognitive biases helps designers build products that work with human psychology instead of against it.',
                'category' => 'user-research',
                'tags' => ['user-testing', 'interaction-design'],
                'content' => '<p>Humans are not rational actors. We take shortcuts, make assumptions, and are influenced by context in ways we rarely notice. For designers, cognitive biases are both a warning and a toolkit — a warning to avoid designing patterns that exploit users, and a toolkit for building experiences that feel natural and reduce cognitive load.</p><h2>The paradox of choice</h2><p>Barry Schwartz\'s research showed that more options lead to more anxiety and less satisfaction. In UI terms: a dropdown with 50 options is harder to use than one with 10, even if users technically have more choice. Reduce options, use progressive disclosure, and apply smart defaults. Hick\'s Law formalises this: decision time increases logarithmically with the number of choices.</p><h2>The peak-end rule</h2><p>Kahneman\'s research found that people judge experiences based on two moments: the peak (the most intense moment, positive or negative) and the end. The middle doesn\'t matter much. For UX, this means the most important moments to design carefully are moments of high stakes — a critical error, a payment confirmation — and the last screen users see in a flow.</p><h2>Anchoring</h2><p>The first piece of information users encounter disproportionately influences subsequent judgements. In pricing, showing a higher price first makes subsequent lower prices seem more reasonable. In complex forms, starting with an easy question reduces abandonment because users are already invested.</p><h2>Ethical responsibility</h2><p>Understanding biases creates a responsibility. Dark patterns — design that exploits biases to manipulate users into actions they didn\'t intend — are increasingly illegal as well as ethically indefensible. Use your knowledge of cognitive biases to reduce friction and help users achieve their goals, not to trick them.</p>',
            ],
        ];

        // ── 6. Insert posts and term relationships ────────────────────────────
        $categoryTaxIds = array_values($categoryIds);
        $tagTaxIds = array_values($tagIds);

        foreach ($posts as $index => $post) {
            $authorId = $authorIds[$index % count($authorIds)];
            $daysAgo = rand(10, 540);
            $postDate = now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $postId = $wp('posts')->insertGetId([
                'post_author' => $authorId,
                'post_date' => $postDate->toDateTimeString(),
                'post_date_gmt' => $postDate->utc()->toDateTimeString(),
                'post_content' => $post['content'],
                'post_title' => $post['title'],
                'post_excerpt' => $post['excerpt'],
                'post_status' => 'publish',
                'comment_status' => 'open',
                'ping_status' => 'open',
                'post_name' => $post['slug'],
                'post_modified' => $postDate->toDateTimeString(),
                'post_modified_gmt' => $postDate->utc()->toDateTimeString(),
                'post_parent' => 0,
                'guid' => 'http://localhost/?p='.($index + 1),
                'menu_order' => 0,
                'post_type' => 'post',
                'post_mime_type' => '',
                'comment_count' => 0,
            ]);

            // Category
            $catTtId = $categoryIds[$post['category']] ?? $categoryTaxIds[0];
            $wp('term_relationships')->insert(['object_id' => $postId, 'term_taxonomy_id' => $catTtId, 'term_order' => 0]);

            // Tags
            foreach ($post['tags'] as $tagSlug) {
                $tagTtId = $tagIds[$tagSlug] ?? null;
                if ($tagTtId) {
                    $wp('term_relationships')->insert(['object_id' => $postId, 'term_taxonomy_id' => $tagTtId, 'term_order' => 0]);
                }
            }
        }

        // ── 7. Update term_taxonomy counts ────────────────────────────────────
        foreach (array_merge(array_values($categoryIds), array_values($tagIds)) as $ttId) {
            $count = $wp('term_relationships')->where('term_taxonomy_id', $ttId)->count();
            $wp('term_taxonomy')->where('term_taxonomy_id', $ttId)->update(['count' => $count]);
        }

        $this->command->info('Seeded 20 Product Design / UX posts with '.count($authorIds).' authors.');
    }
}
