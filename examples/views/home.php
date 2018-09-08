<?php $this->layout('layout') ?>
<?php $this->set('title', 'Hello world!') ?>

<p>Hello from view! <?= isset($foo) ? $foo : null ?></p>

<?php $this->start('scripts') ?>
<script>
  console.log('Hello from view!')
</script>
<?php $this->end() ?>
