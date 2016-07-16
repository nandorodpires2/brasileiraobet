SELECT 	usuario.usuario_nome,
			usuario.usuario_email,
			ifnull(sum(aposta.aposta_vencedora_valor), 0) as premio
FROM 		`aposta`
			INNER JOIN `partida` ON aposta.partida_id = partida.partida_id
			INNER JOIN `time` AS `t1` ON partida.time_id_mandante = t1.time_id
			INNER JOIN `time` AS `t2` ON partida.time_id_visitante = t2.time_id
			INNER JOIN `usuario` ON aposta.usuario_id = usuario.usuario_id
WHERE 	(aposta_vencedora = 1)
GROUP BY `usuario`.`usuario_id`
ORDER BY sum(aposta.aposta_vencedora_valor) DESC
