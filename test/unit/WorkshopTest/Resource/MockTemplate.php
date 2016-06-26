<!DOCTYPE HTML>
<html>
<?php echo \AtomPie\Gui\Component\Template::HEADER; ?>
<body>

<?php echo (isset($this->MockElement)) ? $this->MockElement : ''; ?>
<?php echo (isset($this->MockView)) ? $this->MockView : ''; ?>
<?php echo (isset($this->MockForm)) ? $this->MockForm : ''; ?>

</body>
</html>
